<?php

declare(strict_types=1);

namespace App\Contracts\Clip;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface for clip-related business logic operations.
 *
 * This contract defines all methods for managing clips including
 * search, filtering, moderation, and statistics.
 */
interface ClipServiceInterface
{
    /**
     * Submit a clip for a user.
     */
    public function submitClip(User $user, string $clipId): Clip;

    /**
     * Get clips for a user with pagination.
     */
    public function getUserClips(User $user, ?int $perPage = null): LengthAwarePaginator;

    /**
     * Get clips for a specific broadcaster.
     */
    public function getBroadcasterClips(int $broadcasterId, ?int $perPage = null): LengthAwarePaginator;

    /**
     * Get featured/popular clips.
     */
    public function getFeaturedClips(?int $limit = null): Collection;

    /**
     * Get recent clips.
     */
    public function getRecentClips(?int $limit = null): Collection;

    /**
     * Search clips by title or tags with improved security.
     */
    public function searchClips(string $query, ?int $perPage = null): LengthAwarePaginator;

    /**
     * Get clip statistics for a user.
     */
    public function getUserStats(User $user): array;

    /**
     * Check if user can submit more clips (rate limiting).
     */
    public function canUserSubmitClip(User $user): bool;

    /**
     * Get clips by game/category.
     */
    public function getClipsByGame(int $gameId, ?int $perPage = null): LengthAwarePaginator;

    /**
     * Toggle featured status for a clip (admin only).
     */
    public function toggleFeatured(Clip $clip): bool;

    /**
     * Delete a clip.
     */
    public function deleteClip(Clip $clip): bool;
}
