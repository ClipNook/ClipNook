<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

use App\Services\Twitch\DTOs\ClipData;
use App\Services\Twitch\DTOs\PaginationData;

interface ClipsInterface
{
    /**
     * Get clips for a broadcaster
     *
     * @param  string  $broadcasterId  Broadcaster user ID
     * @param  int  $limit  Number of clips (1-100)
     * @param  string|null  $after  Cursor for pagination
     * @param  string|null  $before  Cursor for pagination
     * @param  \DateTimeInterface|null  $startedAt  Filter by start date
     * @param  \DateTimeInterface|null  $endedAt  Filter by end date
     * @return PaginationData<ClipData>
     */
    public function getClips(
        string $broadcasterId,
        int $limit = 20,
        ?string $after = null,
        ?string $before = null,
        ?\DateTimeInterface $startedAt = null,
        ?\DateTimeInterface $endedAt = null
    ): PaginationData;

    /**
     * Get a specific clip by ID
     */
    public function getClipById(string $clipId): ?ClipData;

    /**
     * Get clips by multiple IDs
     *
     * @param  array<string>  $clipIds  Maximum 100 IDs
     * @return array<ClipData>
     */
    public function getClipsByIds(array $clipIds): array;

    /**
     * Create a clip from a broadcaster's stream
     *
     * @param  bool  $hasDelay  If true, adds a delay before capturing
     * @return array{id: string, edit_url: string}
     */
    public function createClip(string $broadcasterId, bool $hasDelay = false): array;

    /**
     * Fetch a game by id from the Helix API
     *
     * @return array<string, mixed>|null
     */
    public function getGameById(string $gameId): ?array;

    /**
     * Fetch a video by id from the Helix API
     *
     * @return array<string, mixed>|null
     */
    public function getVideoById(string $videoId): ?array;
}
