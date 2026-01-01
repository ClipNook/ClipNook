<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model for managing granular clip permissions granted by broadcasters to users.
 *
 * This model handles the relationship between broadcasters and users who have
 * been granted specific permissions to manage clips on behalf of the broadcaster.
 * Permissions include submitting, editing, deleting, and moderating clips.
 */
class BroadcasterClipPermission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'broadcaster_id',
        'user_id',
        'can_submit_clips',
        'can_edit_clips',
        'can_delete_clips',
        'can_moderate_clips',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'can_submit_clips'   => 'boolean',
        'can_edit_clips'     => 'boolean',
        'can_delete_clips'   => 'boolean',
        'can_moderate_clips' => 'boolean',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'can_submit_clips'   => false,
        'can_edit_clips'     => false,
        'can_delete_clips'   => false,
        'can_moderate_clips' => false,
    ];

    /**
     * Get the broadcaster that granted this permission.
     */
    public function broadcaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broadcaster_id');
    }

    /**
     * Get the user that received this permission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if this permission allows clip submission.
     */
    public function allowsSubmission(): bool
    {
        return $this->can_submit_clips;
    }

    /**
     * Check if this permission allows clip editing.
     */
    public function allowsEditing(): bool
    {
        return $this->can_edit_clips;
    }

    /**
     * Check if this permission allows clip deletion.
     */
    public function allowsDeletion(): bool
    {
        return $this->can_delete_clips;
    }

    /**
     * Check if this permission allows clip moderation.
     */
    public function allowsModeration(): bool
    {
        return $this->can_moderate_clips;
    }

    /**
     * Grant all permissions to this user.
     */
    public function grantAllPermissions(): void
    {
        $this->update([
            'can_submit_clips'   => true,
            'can_edit_clips'     => true,
            'can_delete_clips'   => true,
            'can_moderate_clips' => true,
        ]);
    }

    /**
     * Revoke all permissions from this user.
     */
    public function revokeAllPermissions(): void
    {
        $this->update([
            'can_submit_clips'   => false,
            'can_edit_clips'     => false,
            'can_delete_clips'   => false,
            'can_moderate_clips' => false,
        ]);
    }

    /**
     * Get a human-readable description of the granted permissions.
     */
    public function getPermissionSummaryAttribute(): string
    {
        $permissions = [];

        if ($this->can_submit_clips) {
            $permissions[] = 'submit';
        }
        if ($this->can_edit_clips) {
            $permissions[] = 'edit';
        }
        if ($this->can_delete_clips) {
            $permissions[] = 'delete';
        }
        if ($this->can_moderate_clips) {
            $permissions[] = 'moderate';
        }

        return empty($permissions) ? 'No permissions' : ucfirst(implode(', ', $permissions));
    }
}
