<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\ClipModerated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core Twitch Data
        'uuid',
        'twitch_clip_id',
        'title',
        'description',
        'url',
        'thumbnail_url',
        'local_thumbnail_path',
        'duration',
        'view_count',
        'created_at_twitch',
        'clip_creator_name',

        // Relationships - must match migration order!
        'game_id',
        'status',
        'submitter_id',
        'submitted_at',
        'broadcaster_id',

        // Moderation
        'moderation_reason',
        'moderated_by',
        'moderated_at',

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
        'status'            => \App\Enums\ClipStatus::class,
    ];

    protected $attributes = [
        'status'     => 'pending',
        'upvotes'    => 0,
        'downvotes'  => 0,
        'view_count' => 0,
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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

    public function votes(): HasMany
    {
        return $this->hasMany(ClipVote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ClipComment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ClipReport::class);
    }

    // Scopes
    public function scopeWithRelations($query)
    {
        return $query->with([
            'submitter:id,twitch_display_name,twitch_login,twitch_avatar',
            'broadcaster:id,twitch_display_name,twitch_login,twitch_avatar',
            'moderator:id,twitch_display_name,twitch_login',
            'game:id,name,box_art_url',
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', \App\Enums\ClipStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', \App\Enums\ClipStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', \App\Enums\ClipStatus::REJECTED);
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', \App\Enums\ClipStatus::FLAGGED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === \App\Enums\ClipStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === \App\Enums\ClipStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === \App\Enums\ClipStatus::REJECTED;
    }

    public function isFlagged(): bool
    {
        return $this->status === \App\Enums\ClipStatus::FLAGGED;
    }

    public function approve(?User $moderator = null): void
    {
        $this->update([
            'status'            => \App\Enums\ClipStatus::APPROVED,
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
            'status'            => \App\Enums\ClipStatus::REJECTED,
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
            'status'            => \App\Enums\ClipStatus::FLAGGED,
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
            \App\Enums\ClipStatus::APPROVED => 'success',
            \App\Enums\ClipStatus::REJECTED => 'danger',
            \App\Enums\ClipStatus::FLAGGED  => 'warning',
            default                         => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            \App\Enums\ClipStatus::APPROVED => __('clip.status.approved'),
            \App\Enums\ClipStatus::REJECTED => __('clip.status.rejected'),
            \App\Enums\ClipStatus::FLAGGED  => __('clip.status.flagged'),
            default                         => __('clip.status.pending'),
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

    public function getThumbnailUrlAttribute(): string
    {
        // Use local thumbnail if available, otherwise fall back to Twitch URL
        if ($this->local_thumbnail_path && Storage::disk('public')->exists($this->local_thumbnail_path)) {
            return Storage::disk('public')->url($this->local_thumbnail_path);
        }

        return $this->attributes['thumbnail_url'] ?? '';
    }
}
