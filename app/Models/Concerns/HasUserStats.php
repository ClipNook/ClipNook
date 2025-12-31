<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasUserStats
{
    public function getStatsAttribute(): array
    {
        return [
            'total_clips'       => $this->submittedClips()->count(),
            'approved_clips'    => $this->approvedClips()->count(),
            'pending_clips'     => $this->pendingClips()->count(),
            'rejected_clips'    => $this->rejectedClips()->count(),
            'featured_clips'    => $this->featuredClips()->count(),
            'broadcaster_clips' => $this->broadcasterClips()->count(),
            'moderated_clips'   => $this->moderatedClips()->count(),
            'join_date'         => $this->created_at?->format('M j, Y'),
            'last_activity'     => $this->last_activity_at?->diffForHumans(),
            'activity_level'    => $this->activity_level,
            'profile_complete'  => $this->profile_complete,
        ];
    }

    public function isActive(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->diffInDays(now()) <= 30;
    }

    public function getActivityLevelAttribute(): string
    {
        if (! $this->last_activity_at) {
            return 'inactive';
        }

        $days = $this->last_activity_at->diffInDays(now());

        return match (true) {
            $days <= 1  => 'very_active',
            $days <= 7  => 'active',
            $days <= 30 => 'moderately_active',
            default     => 'inactive',
        };
    }

    public function getProfileCompleteAttribute(): bool
    {
        return ! empty($this->description) &&
               ! empty($this->twitch_display_name) &&
               ! empty($this->avatar_source);
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
