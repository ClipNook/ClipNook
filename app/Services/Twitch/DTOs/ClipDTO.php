<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

final readonly class ClipDTO
{
    public function __construct(
        public string $id,
        public string $url,
        public string $embedUrl,
        public string $broadcasterId,
        public string $broadcasterName,
        public string $creatorId,
        public string $creatorName,
        public string $videoId,
        public ?string $gameId,
        public string $language,
        public string $title,
        public int $viewCount,
        public string $createdAt,
        public string $thumbnailUrl,
        public int $duration,
        public ?int $vodOffset,
        public bool $isFeatured,
    ) {}
}
