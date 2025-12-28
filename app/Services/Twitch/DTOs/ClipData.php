<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

use DateTimeImmutable;

readonly class ClipData
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
        public string $gameId,
        public string $language,
        public string $title,
        public int $viewCount,
        public DateTimeImmutable $createdAt,
        public string $thumbnailUrl,
        public float $duration,
        public int $vodOffset,
        public bool $isFeatured = false,
    ) {}

    /**
     * Create from API response
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            url: $data['url'],
            embedUrl: $data['embed_url'],
            broadcasterId: $data['broadcaster_id'],
            broadcasterName: $data['broadcaster_name'],
            creatorId: $data['creator_id'],
            creatorName: $data['creator_name'],
            videoId: $data['video_id'] ?? '',
            gameId: $data['game_id'],
            language: $data['language'],
            title: $data['title'],
            viewCount: $data['view_count'],
            createdAt: new DateTimeImmutable($data['created_at']),
            thumbnailUrl: $data['thumbnail_url'],
            duration: (float) $data['duration'],
            vodOffset: $data['vod_offset'] ?? 0,
            isFeatured: $data['is_featured'] ?? false,
        );
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'url'              => $this->url,
            'embed_url'        => $this->embedUrl,
            'broadcaster_id'   => $this->broadcasterId,
            'broadcaster_name' => $this->broadcasterName,
            'creator_id'       => $this->creatorId,
            'creator_name'     => $this->creatorName,
            'video_id'         => $this->videoId,
            'game_id'          => $this->gameId,
            'language'         => $this->language,
            'title'            => $this->title,
            'view_count'       => $this->viewCount,
            'created_at'       => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
            'thumbnail_url'    => $this->thumbnailUrl,
            'duration'         => $this->duration,
            'vod_offset'       => $this->vodOffset,
            'is_featured'      => $this->isFeatured,
        ];
    }
}
