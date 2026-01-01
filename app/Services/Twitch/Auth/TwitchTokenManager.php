<?php

declare(strict_types=1);

namespace App\Services\Twitch\Auth;

use App\Events\TwitchTokenRefreshed;
use App\Models\User;
use App\Services\Twitch\DTOs\TokenDTO;
use App\Services\Twitch\Exceptions\TwitchApiException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Service for managing Twitch OAuth tokens.
 */
class TwitchTokenManager
{
    private const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * Refresh an access token using a refresh token.
     *
     * @throws TwitchApiException
     */
    public function refreshUserToken(string $refreshToken): array
    {
        $lockKey = 'twitch_token_refresh_'.Auth::id();
        $lock    = Cache::lock($lockKey, 30);

        try {
            $lock->block(10); // Wait up to 10 seconds

            $response = Http::asForm()->post(self::TOKEN_URL, [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (! $response->successful()) {
                throw TwitchApiException::tokenRefreshFailed('Failed to refresh Twitch token: '.$response->body());
            }

            return $response->json();
        } finally {
            $lock->release();
        }
    }

    /**
     * Update user tokens in database.
     */
    public function updateUserTokens(User $user, TokenDTO $token): void
    {
        $user->update([
            'twitch_access_token'     => $token->accessToken,
            'twitch_refresh_token'    => $token->refreshToken,
            'twitch_token_expires_at' => now()->addSeconds($token->expiresIn),
        ]);

        TwitchTokenRefreshed::dispatch($user->id, true);
    }

    /**
     * Check if token is expired.
     */
    public function isTokenExpired(?int $expiresAt): bool
    {
        return $expiresAt && time() >= $expiresAt;
    }

    /**
     * Get app access token for server-to-server requests.
     *
     * @throws TwitchApiException
     */
    public function getAppAccessToken(): string
    {
        $cacheKey = 'twitch_app_access_token';

        return Cache::remember($cacheKey, 3600, function () { // Cache for 1 hour
            $response = Http::asForm()->post(self::TOKEN_URL, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (! $response->successful()) {
                throw TwitchApiException::appTokenFetchFailed('Failed to fetch app access token: '.$response->body());
            }

            $data = $response->json();

            return $data['access_token'];
        });
    }

    /**
     * Get a valid access token for the user, refreshing if necessary.
     *
     * @throws TwitchApiException
     */
    public function getValidAccessToken(User $user): string
    {
        $accessToken  = $user->twitch_access_token;
        $refreshToken = $user->twitch_refresh_token;
        $expiresAt    = $user->twitch_token_expires_at?->timestamp;

        if (! $accessToken || ! $refreshToken) {
            throw TwitchApiException::authenticationRequired('User has no Twitch tokens');
        }

        if ($this->isTokenExpired($expiresAt)) {
            $refreshedData = $this->refreshUserToken($refreshToken);
            $tokenDTO      = new TokenDTO(
                accessToken: $refreshedData['access_token'],
                refreshToken: $refreshedData['refresh_token'] ?? $refreshToken,
                expiresIn: $refreshedData['expires_in'],
                tokenType: $refreshedData['token_type'],
                scope: is_array($refreshedData['scope']) ? implode(' ', $refreshedData['scope']) : ($refreshedData['scope'] ?? null),
                issuedAt: time(),
            );
            $this->updateUserTokens($user, $tokenDTO);
            $accessToken = $tokenDTO->accessToken;
        }

        return $accessToken;
    }
}
