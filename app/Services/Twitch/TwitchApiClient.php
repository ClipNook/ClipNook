<?php

namespace App\Services\Twitch;

use App\Services\Twitch\Exceptions\TwitchApiException;
use Illuminate\Support\Facades\Http;

class TwitchApiClient
{
    public function __construct(
        protected readonly string $clientId,
        protected readonly string $clientSecret,
        protected readonly int $timeout = 30,
    ) {}

    public function makeRequest(string $endpoint, array $params = [], ?string $accessToken = null): array
    {
        // Ensure endpoint doesn't start with slash to avoid double slashes
        $endpoint = ltrim($endpoint, '/');
        $url      = "https://api.twitch.tv/helix/{$endpoint}";

        $headers = [
            'Client-Id' => $this->clientId,
        ];

        if ($accessToken) {
            $headers['Authorization'] = "Bearer {$accessToken}";
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->get($url, $params);

        if ($response->failed()) {
            throw TwitchApiException::requestFailed($response->status(), $response->body());
        }

        return $response->json();
    }

    public function exchangeCodeForToken(string $code, string $redirectUri): array
    {
        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post('https://id.twitch.tv/oauth2/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code'          => $code,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $redirectUri,
            ]);

        if ($response->failed()) {
            throw TwitchApiException::codeExchangeFailed($response->body());
        }

        return $response->json();
    }

    /**
     * Get game information by ID
     */
    public function getGame(string $gameId, ?string $accessToken = null): ?array
    {
        $response = $this->makeRequest('games', ['id' => $gameId], $accessToken);

        return $response['data'][0] ?? null;
    }
}
