<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Clip\SubmitClipAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ClipService
{
    public function __construct(private readonly SubmitClipAction $submitClipAction) {}

    /**
     * Submit a clip for a user
     */
    public function submitClip(User $user, string $clipId): Clip
    {
        return $this->submitClipAction->execute($user, $clipId);
    }

    /**
     * Get clips for a user with pagination
     */
    public function getUserClips(User $user, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('constants.pagination.default_per_page');

        return Clip::where('user_id', $user->id)
            ->with(['broadcaster', 'game'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get clips for a specific broadcaster
     */
    public function getBroadcasterClips(int $broadcasterId, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('constants.pagination.default_per_page');

        return Clip::where('broadcaster_id', $broadcasterId)
            ->with(['user', 'game'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get featured/popular clips
     */
    public function getFeaturedClips(?int $limit = null): Collection
    {
        $limit ??= config('constants.limits.featured_clips');

        return Cache::remember(
            'featured_clips',
            now()->addMinutes(config('constants.cache.featured_clips_minutes')),
            fn() => Clip::with(['user', 'broadcaster', 'game'])
                ->where('is_featured', true)
                ->orderBy('view_count', 'desc')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get recent clips
     */
    public function getRecentClips(?int $limit = null): Collection
    {
        $limit ??= config('constants.limits.recent_clips');

        return Cache::remember(
            'recent_clips',
            now()->addMinutes(config('constants.cache.recent_clips_minutes')),
            fn() => Clip::with(['user', 'broadcaster', 'game'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Search clips by title or tags with improved security
     */
    public function searchClips(string $query, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('constants.pagination.default_per_page');
        // Sanitize and prepare search query
        $searchTerm = trim($query);
        $searchTerm = preg_replace('/[^\w\s\-]/', '', $searchTerm); // Remove special characters

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            return Clip::whereRaw('1 = 0')->paginate($perPage); // Return empty result
        }

        return Clip::search($searchTerm)
            ->orWhere(function ($q) use ($searchTerm) {
                $q->whereJsonContains('tags', $searchTerm);
            })
            ->withRelations() // Use the optimized scope
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get clip statistics for a user
     */
    public function getUserStats(User $user): array
    {
        return Cache::remember("user_clip_stats_{$user->id}", now()->addHours(config('constants.cache.user_stats_hours')), function () use ($user) {
            $stats = Clip::where('user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total_clips,
                    COALESCE(SUM(view_count), 0) as total_views,
                    COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured_clips,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as recent_submissions
                ', [now()->subDays(7)])
                ->first();

            return [
                'total_clips'        => (int) $stats->total_clips,
                'total_views'        => (int) $stats->total_views,
                'featured_clips'     => (int) $stats->featured_clips,
                'recent_submissions' => (int) $stats->recent_submissions,
            ];
        });
    }

    /**
     * Check if user can submit more clips (rate limiting)
     */
    public function canUserSubmitClip(User $user): bool
    {
        $submittedToday = Clip::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return $submittedToday < config('constants.limits.daily_clip_submissions');
    }

    /**
     * Get clips by game/category
     */
    public function getClipsByGame(int $gameId, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('constants.pagination.default_per_page');

        return Clip::where('game_id', $gameId)
            ->with(['user', 'broadcaster'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Toggle featured status for a clip (admin only)
     */
    public function toggleFeatured(Clip $clip): bool
    {
        $clip->update(['is_featured' => ! $clip->is_featured]);
        Cache::forget('featured_clips');

        return $clip->is_featured;
    }

    /**
     * Delete a clip
     */
    public function deleteClip(Clip $clip): bool
    {
        // Clear related caches
        Cache::forget('featured_clips');
        Cache::forget('recent_clips');
        Cache::forget("user_clip_stats_{$clip->user_id}");

        return $clip->delete();
    }
}
