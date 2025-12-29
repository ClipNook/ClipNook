<?php

declare(strict_types=1);

namespace App\Services\Twitch\OAuth;

use App\Services\Twitch\Contracts\HttpClientInterface;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\DTOs\TokenData;
use App\Services\Twitch\DTOs\UserData;
use App\Services\Twitch\Exceptions\AuthenticationException;
use App\Services\Twitch\Exceptions\ValidationException;

class OAuthService implements OAuthInterface
{
    private readonly string $clientId;

    private readonly string $clientSecret;

    private readonly string $redirectUri;

    /** @var array<string> */
    private readonly array $scopes;

    private readonly string $authUrl;

    private readonly string $apiUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        array $config,
    ) {
        $this->clientId     = $config['client_id'] ?? throw new \InvalidArgumentException('Missing client_id');
        $this->clientSecret = $config['client_secret'] ?? throw new \InvalidArgumentException('Missing client_secret');
        $this->redirectUri  = $config['redirect_uri'] ?? throw new \InvalidArgumentException('Missing redirect_uri');
        $this->scopes       = is_array($config['scopes']) ? $config['scopes'] : explode(' ', $config['scopes']);
        $this->authUrl      = $config['auth_url'] ?? 'https://id.twitch.tv/oauth2';
        $this->apiUrl       = $config['api_url'] ?? 'https://api.twitch.tv/helix';
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl(string $state, array $scopes = []): string
    {
        if (empty($state)) {
            throw new ValidationException('State parameter is required for CSRF protection');
        }

        $scopesToUse = ! empty($scopes) ? $scopes : $this->scopes;

        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', $scopesToUse),
            'state'         => $state,
            'force_verify'  => 'true', // GDPR: Always ask for consent
        ];

        return $this->authUrl.'/authorize?'.http_build_query($params);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessToken(string $code): TokenData
    {
        if (empty($code)) {
            throw new ValidationException('Authorization code is required');
        }

        $response = $this->httpClient->post($this->authUrl.'/token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->redirectUri,
        ], [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        if (! isset($response['access_token'])) {
            throw AuthenticationException::fromResponse($response, $this->httpClient->getLastStatusCode());
        }

        return TokenData::fromArray($response);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshToken(string $refreshToken): TokenData
    {
        if (empty($refreshToken)) {
            throw new ValidationException('Refresh token is required');
        }

        $response = $this->httpClient->post($this->authUrl.'/token', [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ], [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        if (! isset($response['access_token'])) {
            throw AuthenticationException::fromResponse($response, $this->httpClient->getLastStatusCode());
        }

        return TokenData::fromArray($response);
    }

    /**
     * {@inheritDoc}
     */
    public function revokeToken(string $token): bool
    {
        if (empty($token)) {
            throw new ValidationException('Token is required');
        }

        try {
            $this->httpClient->post($this->authUrl.'/revoke', [
                'client_id' => $this->clientId,
                'token'     => $token,
            ], [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);

            return $this->httpClient->getLastStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateToken(string $token): UserData
    {
        if (empty($token)) {
            throw new ValidationException('Token is required');
        }

        $response = $this->httpClient->get($this->authUrl.'/validate', [], [
            'Authorization' => 'OAuth '.$token,
        ]);

        return UserData::fromArray($response);
    }

    /**
     * Fetch the current user using the Helix users endpoint.
     */
    public function getUser(string $accessToken): UserData
    {
        if (empty($accessToken)) {
            throw new ValidationException('Access token is required');
        }

        $response = $this->httpClient->get($this->apiUrl.'/users', [], [
            'Authorization' => 'Bearer '.$accessToken,
            'Client-ID'     => $this->clientId,
        ]);

        // Helix returns data as ['data' => [ {user} ] ]
        if (! isset($response['data'][0]) || ! is_array($response['data'][0])) {
            throw AuthenticationException::fromResponse($response, $this->httpClient->getLastStatusCode());
        }

        return UserData::fromArray($response['data'][0]);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserById(string $accessToken, string $userId): UserData
    {
        if (empty($accessToken)) {
            throw new ValidationException('Access token is required');
        }

        if (empty($userId)) {
            throw new ValidationException('User id is required');
        }

        $response = $this->httpClient->get($this->apiUrl.'/users', ['id' => $userId], [
            'Authorization' => 'Bearer '.$accessToken,
            'Client-ID'     => $this->clientId,
        ]);

        if (! isset($response['data'][0]) || ! is_array($response['data'][0])) {
            throw AuthenticationException::fromResponse($response, $this->httpClient->getLastStatusCode());
        }

        return UserData::fromArray($response['data'][0]);
    }
}
