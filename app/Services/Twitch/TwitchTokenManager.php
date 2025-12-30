<?php

namespace App\Services\Twitch;

use App\Services\Twitch\Exceptions\TwitchApiException;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Http;

class TwitchTokenManager
{
    public function __construct(
        protected readonly string $clientId,
        protected readonly string $clientSecret,
        protected readonly Cache $cache,
        protected readonly int $timeout = 30,
    ) {}

    public function getAppAccessToken(): string
    {
        $cacheKey = 'twitch_app_access_token';

        return $this->cache->remember($cacheKey, now()->addMinutes(55), function () {
            return $this->fetchAppAccessToken();
        });
    }

    public function refreshUserToken(string $refreshToken): array
    {
        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post('https://id.twitch.tv/oauth2/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        if ($response->failed()) {
            throw new TwitchApiException('Failed to refresh user token');
        }

        return $response->json();
    }

    public function validateToken(string $accessToken): bool
    {
        $response = Http::withHeaders([
            'Authorization' => "OAuth {$accessToken}",
        ])->get('https://id.twitch.tv/oauth2/validate');

        return $response->successful();
    }

    protected function fetchAppAccessToken(): string
    {
        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post('https://id.twitch.tv/oauth2/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new TwitchApiException('Failed to fetch app access token');
        }

        $data = $response->json();

        return $data['access_token'];
    }
}
