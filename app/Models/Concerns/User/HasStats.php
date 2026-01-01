<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

/**
 * Handles user statistics and metrics.
 */
trait HasStats
{
    /**
     * Get total clips submitted by user.
     */
    public function getTotalClipsSubmittedAttribute(): int
    {
        return $this->clips()->count();
    }

    /**
     * Get approved clips count.
     */
    public function getApprovedClipsCountAttribute(): int
    {
        return $this->clips()->where('status', 'approved')->count();
    }

    /**
     * Get rejected clips count.
     */
    public function getRejectedClipsCountAttribute(): int
    {
        return $this->clips()->where('status', 'rejected')->count();
    }

    /**
     * Get total upvotes received on user's clips.
     */
    public function getTotalUpvotesReceivedAttribute(): int
    {
        return $this->clips()->sum('upvotes');
    }

    /**
     * Get total downvotes received on user's clips.
     */
    public function getTotalDownvotesReceivedAttribute(): int
    {
        return $this->clips()->sum('downvotes');
    }

    /**
     * Get user's clip score (total upvotes - downvotes).
     */
    public function getClipScoreAttribute(): int
    {
        return $this->total_upvotes_received - $this->total_downvotes_received;
    }

    /**
     * Get user's reputation score.
     */
    public function getReputationScoreAttribute(): float
    {
        $totalClips = $this->total_clips_submitted;
        if ($totalClips === 0) {
            return 0.0;
        }

        $approvedRatio = $this->approved_clips_count / $totalClips;
        $scoreRatio    = $this->clip_score > 0 ? min($this->clip_score / ($totalClips * 10), 1) : 0;

        return round(($approvedRatio * 0.7 + $scoreRatio * 0.3) * 100, 1);
    }

    /**
     * Get user's activity level.
     */
    public function getActivityLevelAttribute(): string
    {
        $clipsThisMonth = $this->clips()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return match (true) {
            $clipsThisMonth >= 50 => 'Very Active',
            $clipsThisMonth >= 20 => 'Active',
            $clipsThisMonth >= 5  => 'Moderate',
            $clipsThisMonth >= 1  => 'Low',
            default               => 'Inactive',
        };
    }
}
