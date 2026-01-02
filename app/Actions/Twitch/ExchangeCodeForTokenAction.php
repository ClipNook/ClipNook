<?php

declare(strict_types=1);

namespace App\Actions\Twitch;

use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\DTOs\TokenDTO;
use Exception;

use function config;
use function implode;
use function is_array;
use function time;

final class ExchangeCodeForTokenAction
{
    public function __construct(
        private readonly TwitchApiClientInterface $apiClient,
    ) {}

    public function execute(string $code): ?TokenDTO
    {
        try {
            $data = $this->apiClient->exchangeCodeForToken(
                $code,
                config('twitch.redirect_uri')
            );

            return new TokenDTO(
                accessToken: $data['access_token'],
                refreshToken: $data['refresh_token'],
                expiresIn: $data['expires_in'],
                tokenType: $data['token_type'],
                scope: is_array($data['scope']) ? implode(' ', $data['scope']) : ($data['scope'] ?? null),
                issuedAt: time(),
            );
        } catch (Exception $e) {
            return null;
        }
    }
}
