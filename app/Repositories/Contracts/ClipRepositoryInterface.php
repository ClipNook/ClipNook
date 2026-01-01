<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ClipStatus;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Clip repository interface.
 */
interface ClipRepositoryInterface extends RepositoryInterface
{
    /**
     * Find clip by Twitch clip ID.
     */
    public function findByTwitchId(string $twitchClipId): ?Clip;

    /**
     * Get clips by status.
     */
    public function getByStatus(ClipStatus $status): Collection;

    /**
     * Get clips by submitter.
     */
    public function getBySubmitter(User $user): Collection;

    /**
     * Get clips by broadcaster.
     */
    public function getByBroadcaster(User $broadcaster): Collection;

    /**
     * Get pending clips.
     */
    public function getPending(): Collection;

    /**
     * Get approved clips.
     */
    public function getApproved(): Collection;

    /**
     * Get rejected clips.
     */
    public function getRejected(): Collection;

    /**
     * Get featured clips.
     */
    public function getFeatured(): Collection;

    /**
     * Get clips for moderation.
     */
    public function getForModeration(): Collection;

    /**
     * Approve a clip.
     */
    public function approveClip(Clip $clip, ?User $moderator = null): bool;

    /**
     * Reject a clip.
     */
    public function rejectClip(Clip $clip, string $reason, ?User $moderator = null): bool;

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Clip $clip): bool;

    /**
     * Get clips by game.
     */
    public function getByGame(int $gameId): Collection;

    /**
     * Get popular clips (by view count).
     */
    public function getPopular(?int $limit = null): Collection;

    /**
     * Get recent clips.
     */
    public function getRecent(?int $limit = null): Collection;

    /**
     * Search clips by title or description.
     */
    public function search(string $query): Collection;

    /**
     * Get clip statistics.
     */
    public function getStats(): array;
}
