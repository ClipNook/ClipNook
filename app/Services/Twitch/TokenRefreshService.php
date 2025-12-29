<?php

namespace App\Services\Twitch;

use App\Models\User;
use App\Services\Twitch\Contracts\OAuthInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenRefreshService
{
    public function __construct(
        private readonly OAuthInterface $oauth
    ) {}

    /**
     * Get a valid access token for the user
     * Tries: user token -> refresh token -> app token
     */
    public function getValidToken(User $user): ?string
    {
        // Check if current token is still valid
        if ($this->hasValidToken($user)) {
            return $user->twitch_access_token;
        }

        // Try to refresh the token
        if ($this->refreshUserToken($user)) {
            return $user->twitch_access_token;
        }

        // Fallback to app token (client credentials)
        return $this->getAppToken();
    }

    /**
     * Check if user has a valid, non-expired token
     */
    private function hasValidToken(User $user): bool
    {
        if (empty($user->twitch_access_token)) {
            return false;
        }

        if (empty($user->twitch_token_expires_at)) {
            return true; // No expiry set, assume valid
        }

        return Carbon::now()->lt($user->twitch_token_expires_at);
    }

    /**
     * Attempt to refresh user's token using refresh token
     */
    private function refreshUserToken(User $user): bool
    {
        if (empty($user->twitch_refresh_token)) {
            return false;
        }

        try {
            $tokenData = $this->oauth->refreshToken($user->twitch_refresh_token);

            if (empty($tokenData->accessToken)) {
                return false;
            }

            // Update user tokens
            $user->twitch_access_token  = $tokenData->accessToken;
            $user->twitch_refresh_token = $tokenData->refreshToken ?? $user->twitch_refresh_token;

            if (property_exists($tokenData, 'expiresAt') && $tokenData->expiresAt) {
                $user->twitch_token_expires_at = Carbon::parse($tokenData->expiresAt);
            }

            $user->save();

            return true;

        } catch (\Throwable $e) {
            // Avoid logging user-identifying information
            Log::warning('Token refresh failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Get an app-level access token (client credentials)
     */
    private function getAppToken(): ?string
    {
        try {
            $authUrl = config('services.twitch.auth_url', 'https://id.twitch.tv/oauth2');

            $response = Http::asForm()
                ->timeout(10)
                ->post($authUrl.'/token', [
                    'client_id'     => config('services.twitch.client_id'),
                    'client_secret' => config('services.twitch.client_secret'),
                    'grant_type'    => 'client_credentials',
                ]);

            if ($response->ok() && $response->has('access_token')) {
                return $response->json('access_token');
            }

            // Log status code only; response bodies may contain sensitive details
            Log::warning('Failed to obtain app access token', ['status' => $response->status()]);

            return null;

        } catch (\Throwable $e) {
            Log::error('App token request failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
