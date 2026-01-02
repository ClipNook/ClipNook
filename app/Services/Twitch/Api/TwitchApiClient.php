<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\Exceptions\TwitchApiException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

use function ltrim;

/**
 * Core Twitch API client for making HTTP requests to Twitch Helix API.
 */
final class TwitchApiClient implements TwitchApiClientInterface
{
    private const BASE_URL = 'https://api.twitch.tv/helix';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * Make a request to the Twitch API.
     *
     * @param  string      $endpoint    The API endpoint (without base URL)
     * @param  array       $params      Query parameters
     * @param  string|null $accessToken Optional access token for authenticated requests
     * @return array       The decoded JSON response
     *
     * @throws TwitchApiException
     */
    public function makeRequest(string $endpoint, array $params = [], ?string $accessToken = null): array
    {
        $url = self::BASE_URL.'/'.ltrim($endpoint, '/');

        $httpClient = Http::withHeaders([
            'Client-ID' => $this->clientId,
        ]);

        if ($accessToken) {
            $httpClient = $httpClient->withToken($accessToken);
        }

        $response = $httpClient->get($url, $params);

        if (! $response->successful()) {
            throw TwitchApiException::apiError("Twitch API request failed: {$response->status()} - {$response->body()}", $response->status());
        }

        return $response->json();
    }

    /**
     * Make an authenticated request requiring user token.
     */
    public function makeAuthenticatedRequest(string $endpoint, array $params, ?string $accessToken): array
    {
        if ($accessToken === null) {
            throw new InvalidArgumentException('Access token is required for authenticated requests');
        }

        return $this->makeRequest($endpoint, $params, $accessToken);
    }

    /**
     * Exchange authorization code for access token.
     */
    public function exchangeCodeForToken(string $code, string $redirectUri): array
    {
        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUri,
        ]);

        if (! $response->successful()) {
            throw TwitchApiException::apiError("Token exchange failed: {$response->status()} - {$response->body()}", $response->status());
        }

        return $response->json();
    }
}
