<?php

namespace App\Models;

use App\Events\ClipModerated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core Twitch Data
        'twitch_clip_id',
        'title',
        'description',
        'url',
        'thumbnail_url',
        'duration',
        'view_count',
        'created_at_twitch',

        // User Relationships
        'submitter_id',
        'broadcaster_id',
        'game_id',

        // Moderation
        'status',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'submitted_at',

        // Social Features
        'tags',
        'is_featured',
        'upvotes',
        'downvotes',
    ];

    protected $casts = [
        'created_at_twitch' => 'datetime',
        'submitted_at'      => 'datetime',
        'moderated_at'      => 'datetime',
        'tags'              => 'array',
        'is_featured'       => 'boolean',
        'upvotes'           => 'integer',
        'downvotes'         => 'integer',
        'view_count'        => 'integer',
        'duration'          => 'integer',
    ];

    protected $attributes = [
        'status'     => 'pending',
        'upvotes'    => 0,
        'downvotes'  => 0,
        'view_count' => 0,
    ];

    // Relationships
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function broadcaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broadcaster_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', 'flagged');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isFlagged(): bool
    {
        return $this->status === 'flagged';
    }

    public function approve(?User $moderator = null): void
    {
        $this->update([
            'status'            => 'approved',
            'moderated_by'      => $moderator?->id,
            'moderated_at'      => now(),
            'moderation_reason' => null,
        ]);

        if ($moderator) {
            ClipModerated::dispatch($this, $moderator, 'approve');
        }
    }

    public function reject(string $reason, ?User $moderator = null): void
    {
        $this->update([
            'status'            => 'rejected',
            'moderation_reason' => $reason,
            'moderated_by'      => $moderator?->id,
            'moderated_at'      => now(),
        ]);

        if ($moderator) {
            ClipModerated::dispatch($this, $moderator, 'reject', $reason);
        }
    }

    public function flag(string $reason, ?User $moderator = null): void
    {
        $this->update([
            'status'            => 'flagged',
            'moderation_reason' => $reason,
            'moderated_by'      => $moderator?->id,
            'moderated_at'      => now(),
        ]);

        if ($moderator) {
            ClipModerated::dispatch($this, $moderator, 'flag', $reason);
        }
    }

    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => ! $this->is_featured]);
    }

    public function getScoreAttribute(): int
    {
        return $this->upvotes - $this->downvotes;
    }

    public function getPopularityScoreAttribute(): float
    {
        $ageInHours = $this->created_at->diffInHours(now());
        $score      = $this->score;

        // Reddit-style algorithm: score / (age_in_hours + 2)^1.8
        return $score / pow($ageInHours + 2, 1.8);
    }

    public function getTwitchUrlAttribute(): string
    {
        return "https://clips.twitch.tv/{$this->twitch_clip_id}";
    }

    public function getEmbedUrlAttribute(): string
    {
        return "https://clips.twitch.tv/embed?clip={$this->twitch_clip_id}&parent=".config('app.url');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'flagged'  => 'warning',
            default    => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'approved' => __('clip.status.approved'),
            'rejected' => __('clip.status.rejected'),
            'flagged'  => __('clip.status.flagged'),
            default    => __('clip.status.pending'),
        };
    }

    public function getBroadcasterDisplayNameAttribute(): string
    {
        return $this->broadcaster?->name ?? 'Unknown Broadcaster';
    }

    public function getBroadcasterUrlAttribute(): string
    {
        if ($this->broadcaster?->twitch_login) {
            return "https://twitch.tv/{$this->broadcaster->twitch_login}";
        }

        return '#';
    }

    public function isSubmittedBy(User $user): bool
    {
        return $this->submitter_id === $user->id;
    }

    public function isFromBroadcaster(User $user): bool
    {
        return $this->broadcaster_id === $user->id;
    }

    public function canBeEditedBy(User $user): bool
    {
        // Submitter can always edit their own clips
        if ($this->isSubmittedBy($user)) {
            return true;
        }

        // Broadcaster can edit clips submitted to them
        if ($this->isFromBroadcaster($user)) {
            return true;
        }

        // Check if user has edit permission from broadcaster
        return $user->canEditClipsFor($this->broadcaster);
    }

    public function canBeModeratedBy(User $user): bool
    {
        // Broadcaster can always moderate their own clips
        if ($this->isFromBroadcaster($user)) {
            return true;
        }

        // Check if user has moderation permission from broadcaster
        return $user->canModerateClipsFor($this->broadcaster);
    }

    public function canBeDeletedBy(User $user): bool
    {
        // Submitter can delete their own clips
        if ($this->isSubmittedBy($user)) {
            return true;
        }

        // Broadcaster can delete clips submitted to them
        if ($this->isFromBroadcaster($user)) {
            return true;
        }

        // Check if user has delete permission from broadcaster
        return $user->canDeleteClipsFor($this->broadcaster);
    }

    public function upvote(User $user): bool
    {
        // TODO: Implement proper voting system with vote tracking table
        // For now, simply increment upvotes
        $this->increment('upvotes');

        return true;
    }

    public function downvote(User $user): bool
    {
        // TODO: Implement proper voting system with vote tracking table
        // For now, simply increment downvotes
        $this->increment('downvotes');

        return true;
    }

    public function isPopular(): bool
    {
        return $this->score > 10 && $this->view_count > 100;
    }

    public function isTrending(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24 && $this->score > 5;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->submitted_at->diffForHumans();
    }

    public function getFormattedTagsAttribute(): string
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }
}
