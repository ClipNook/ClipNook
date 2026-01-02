<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClipStatus;
use App\Enums\VoteType;
use App\Models\Concerns\Clip\HasMedia;
use App\Models\Concerns\Clip\HasModeration;
use App\Models\Concerns\Clip\HasOptimizedQueries;
use App\Models\Concerns\Clip\HasVoting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function config;
use function now;

final class Clip extends Model
{
    use HasFactory;
    use HasMedia;
    use HasModeration;
    use HasOptimizedQueries;
    use HasVoting;

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
        'status'            => ClipStatus::class,
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
    public function scopeWithRelations(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->with([
            'submitter:id,twitch_display_name,twitch_login',
            'broadcaster:id,twitch_display_name,twitch_login',
            'moderator:id,twitch_display_name,twitch_login',
            'game:id,name,box_art_url',
        ]);
    }

    public function scopePending(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', ClipStatus::PENDING);
    }

    public function scopeApproved(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', ClipStatus::APPROVED);
    }

    public function scopeRejected(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', ClipStatus::REJECTED);
    }

    public function scopeFlagged(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', ClipStatus::FLAGGED);
    }

    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $searchTerm)
    {
        return $query->where(static function ($q) use ($searchTerm): void {
            $q->where('title', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhere('twitch_clip_id', 'like', "%{$searchTerm}%");
        });
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === ClipStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === ClipStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === ClipStatus::REJECTED;
    }

    public function isFlagged(): bool
    {
        return $this->status === ClipStatus::FLAGGED;
    }

    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => ! $this->is_featured]);
    }

    public function getPopularityScoreAttribute(): float
    {
        $ageInHours = $this->created_at->diffInHours(now());
        $score      = $this->score;

        // Reddit-style algorithm: score / (age_in_hours + 2)^1.8
        return $score / ($ageInHours + 2) ** 1.8;
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
        return $this->status->badgeColor();
    }

    public function getStatusTextAttribute(): string
    {
        return $this->status->label();
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

    public function hasUserVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function getUserVoteType(User $user): ?VoteType
    {
        $vote = $this->votes()->where('user_id', $user->id)->first();

        return $vote?->vote_type;
    }

    public function isFromBroadcaster(User $user): bool
    {
        return $this->broadcaster_id === $user->id;
    }

    public function isSubmittedBy(User $user): bool
    {
        return $this->submitter_id === $user->id;
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
        // Check if user already voted
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === VoteType::UPVOTE) {
                // Already upvoted, remove vote
                $existingVote->delete();
                $this->decrement('upvotes');

                return false;
            }
            // Change from downvote to upvote
            $existingVote->update(['vote_type' => VoteType::UPVOTE]);
            $this->increment('upvotes');
            $this->decrement('downvotes');

            return true;
        }

        // New upvote
        $this->votes()->create([
            'user_id'   => $user->id,
            'vote_type' => VoteType::UPVOTE,
        ]);
        $this->increment('upvotes');

        return true;
    }

    public function downvote(User $user): bool
    {
        // Check if user already voted
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === VoteType::DOWNVOTE) {
                // Already downvoted, remove vote
                $existingVote->delete();
                $this->decrement('downvotes');

                return false;
            }
            // Change from upvote to downvote
            $existingVote->update(['vote_type' => VoteType::DOWNVOTE]);
            $this->decrement('upvotes');
            $this->increment('downvotes');

            return true;
        }

        // New downvote
        $this->votes()->create([
            'user_id'   => $user->id,
            'vote_type' => VoteType::DOWNVOTE,
        ]);
        $this->increment('downvotes');

        return true;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->submitted_at->diffForHumans();
    }
}
