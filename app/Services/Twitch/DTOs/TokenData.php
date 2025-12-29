<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

use DateTimeImmutable;

readonly class TokenData
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
        public array $scopes,
        public string $tokenType = 'bearer',
        public ?DateTimeImmutable $expiresAt = null,
    ) {}

    /**
     * Create from API response
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $expiresAt = isset($data['expires_in'])
            ? (new DateTimeImmutable)->modify("+{$data['expires_in']} seconds")
            : null;

        // Normalize scopes: Twitch may return a string (space-separated) or an array
        $scopes = $data['scope'] ?? [];
        if (is_string($scopes)) {
            $scopes = $scopes === '' ? [] : explode(' ', $scopes);
        }
        $scopes = is_array($scopes) ? $scopes : (array) $scopes;

        return new self(
            accessToken: $data['access_token'],
            refreshToken: $data['refresh_token'] ?? '',
            expiresIn: $data['expires_in'],
            scopes: $scopes,
            tokenType: $data['token_type'] ?? 'bearer',
            expiresAt: $expiresAt,
        );
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt <= new DateTimeImmutable;
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'access_token'  => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in'    => $this->expiresIn,
            'scopes'        => $this->scopes,
            'token_type'    => $this->tokenType,
            'expires_at'    => $this->expiresAt?->format('Y-m-d H:i:s'),
        ];
    }
}
