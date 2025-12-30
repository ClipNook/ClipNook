<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'twitch_clip_id',
        'title',
        'description',
        'url',
        'thumbnail_url',
        'duration',
        'view_count',
        'created_at_twitch',
        'status',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'tags',
        'is_featured',
        'upvotes',
        'downvotes',
    ];

    protected $casts = [
        'created_at_twitch' => 'datetime',
        'moderated_at'      => 'datetime',
        'tags'              => 'array',
        'is_featured'       => 'boolean',
    ];

    protected $attributes = [
        'status'     => 'pending',
        'upvotes'    => 0,
        'downvotes'  => 0,
        'view_count' => 0,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
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
    }

    public function reject(string $reason, ?User $moderator = null): void
    {
        $this->update([
            'status'            => 'rejected',
            'moderation_reason' => $reason,
            'moderated_by'      => $moderator?->id,
            'moderated_at'      => now(),
        ]);
    }

    public function flag(string $reason, ?User $moderator = null): void
    {
        $this->update([
            'status'            => 'flagged',
            'moderation_reason' => $reason,
            'moderated_by'      => $moderator?->id,
            'moderated_at'      => now(),
        ]);
    }

    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => ! $this->is_featured]);
    }

    public function getScoreAttribute(): int
    {
        return $this->upvotes - $this->downvotes;
    }

    public function getDurationFormattedAttribute(): string
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return $minutes > 0
            ? sprintf('%d:%02d', $minutes, $seconds)
            : sprintf('%ds', $seconds);
    }
}
