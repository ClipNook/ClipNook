<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

readonly class VideoDTO
{
    public function __construct(
        public string $id,
        public ?string $streamId,
        public string $userId,
        public string $userLogin,
        public string $userName,
        public string $title,
        public string $description,
        public string $createdAt,
        public string $publishedAt,
        public string $url,
        public string $thumbnailUrl,
        public string $viewable,
        public int $viewCount,
        public string $language,
        public string $type,
        public string $duration,
        public ?array $mutedSegments,
    ) {}
}
