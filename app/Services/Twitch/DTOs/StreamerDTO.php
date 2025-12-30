<?php

namespace App\Services\Twitch\DTOs;

readonly class StreamerDTO
{
    public function __construct(
        public string $id,
        public string $login,
        public string $displayName,
        public string $type,
        public string $broadcasterType,
        public string $description,
        public string $profileImageUrl,
        public string $offlineImageUrl,
        public int $viewCount,
        public string $createdAt,
        public ?string $email = null,
    ) {}
}
