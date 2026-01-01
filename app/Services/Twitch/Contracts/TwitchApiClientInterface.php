<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

interface TwitchApiClientInterface
{
    public function makeRequest(string $endpoint, array $params = [], ?string $accessToken = null): array;

    public function makeAuthenticatedRequest(string $endpoint, array $params, ?string $accessToken): array;

    public function exchangeCodeForToken(string $code, string $redirectUri): array;
}
