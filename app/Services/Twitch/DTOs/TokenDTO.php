<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

readonly class TokenDTO
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
        public string $tokenType,
        public ?string $scope,
        public int $issuedAt = 0, // Timestamp when token was issued
    ) {}

    public function isExpired(int $buffer = 0): bool
    {
        return time() >= ($this->issuedAt + $this->expiresIn - $buffer);
    }

    public function getExpiresAt(): int
    {
        return $this->issuedAt + $this->expiresIn;
    }
}
