<?php

namespace App\Services;

use App\Actions\Clip\SubmitClipAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ClipService
{
    public function __construct(private SubmitClipAction $submitClipAction) {}

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
    public function getUserClips(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Clip::where('user_id', $user->id)
            ->with(['broadcaster', 'game'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get clips for a specific broadcaster
     */
    public function getBroadcasterClips(int $broadcasterId, int $perPage = 15): LengthAwarePaginator
    {
        return Clip::where('broadcaster_id', $broadcasterId)
            ->with(['user', 'game'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get featured/popular clips
     */
    public function getFeaturedClips(int $limit = 10): Collection
    {
        return Cache::remember('featured_clips', now()->addMinutes(30), fn () => Clip::with(['user', 'broadcaster', 'game'])
            ->where('is_featured', true)
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get()
        );
    }

    /**
     * Get recent clips
     */
    public function getRecentClips(int $limit = 20): Collection
    {
        return Cache::remember('recent_clips', now()->addMinutes(15), fn () => Clip::with(['user', 'broadcaster', 'game'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
        );
    }

    /**
     * Search clips by title or tags with improved security
     */
    public function searchClips(string $query, int $perPage = 15): LengthAwarePaginator
    {
        // Sanitize and prepare search query
        $searchTerm = trim($query);
        $searchTerm = preg_replace('/[^\w\s\-]/', '', $searchTerm); // Remove special characters

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            return Clip::whereRaw('1 = 0')->paginate($perPage); // Return empty result
        }

        return Clip::where(function ($q) use ($searchTerm) {
            // Use parameterized queries for better security
            $q->where('title', 'LIKE', '%'.$searchTerm.'%')
                ->orWhereJsonContains('tags', $searchTerm);
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
        return Cache::remember("user_clip_stats_{$user->id}", now()->addHours(1), function () use ($user) {
            $clips = Clip::where('user_id', $user->id);

            return [
                'total_clips'        => $clips->count(),
                'total_views'        => (int) $clips->sum('view_count'),
                'featured_clips'     => $clips->where('is_featured', true)->count(),
                'recent_submissions' => $clips->where('created_at', '>=', now()->subDays(7))->count(),
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

        return $submittedToday < config('clip.daily_limit', 10);
    }

    /**
     * Get clips by game/category
     */
    public function getClipsByGame(int $gameId, int $perPage = 15): LengthAwarePaginator
    {
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
