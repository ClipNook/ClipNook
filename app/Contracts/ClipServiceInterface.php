<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Clip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for clip-related business logic operations.
 *
 * This contract defines all methods for managing clips including
 * search, filtering, moderation, and statistics.
 */
interface ClipServiceInterface
{
	/**
	 * Search for clips with the given query.
	 *
	 * @param  string  $searchTerm  The search term to filter clips
	 * @param  int  $perPage  Number of results per page
	 * @return LengthAwarePaginator<Clip>
	 */
	public function searchClips(string $searchTerm, int $perPage = 15): LengthAwarePaginator;

	/**
	 * Get recent clips with caching.
	 *
	 * @param  int  $limit  Maximum number of clips to retrieve
	 * @return Collection<int, Clip>
	 */
	public function getRecentClips(int $limit = 10): Collection;

	/**
	 * Get featured clips with caching.
	 *
	 * @param  int  $limit  Maximum number of featured clips
	 * @return Collection<int, Clip>
	 */
	public function getFeaturedClips(int $limit = 10): Collection;

	/**
	 * Get clips for a specific broadcaster.
	 *
	 * @param  string  $broadcasterId  The broadcaster's ID
	 * @param  int  $perPage  Number of results per page
	 * @return LengthAwarePaginator<Clip>
	 */
	public function getClipsByBroadcaster(string $broadcasterId, int $perPage = 15): LengthAwarePaginator;

	/**
	 * Get clips for a specific game.
	 *
	 * @param  string  $gameId  The game's ID
	 * @param  int  $perPage  Number of results per page
	 * @return LengthAwarePaginator<Clip>
	 */
	public function getClipsByGame(string $gameId, int $perPage = 15): LengthAwarePaginator;

	/**
	 * Get clips pending moderation.
	 *
	 * @param  int  $perPage  Number of results per page
	 * @return LengthAwarePaginator<Clip>
	 */
	public function getPendingClips(int $perPage = 20): LengthAwarePaginator;

	/**
	 * Get platform-wide statistics.
	 *
	 * @return array{
	 *     total_clips: int,
	 *     approved_clips: int,
	 *     pending_clips: int,
	 *     total_views: int,
	 *     total_broadcasters: int
	 * }
	 */
	public function getPlatformStats(): array;

	/**
	 * Invalidate all clip-related caches.
	 *
	 * @return void
	 */
	public function invalidateCache(): void;
}
