<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;

trait HasClipPermissions
{
    /**
     * Check if user can submit clips
     */
    public function canSubmitClips(): bool
    {
        return $this->is_cutter || $this->is_streamer || $this->isStaff();
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if user can moderate
     */
    public function canModerate(): bool
    {
        return $this->isStaff() || $this->is_streamer;
    }

    /**
     * Check if user can submit clips for a specific broadcaster
     */
    public function canSubmitClipsFor(User $broadcaster): bool
    {
        // Broadcaster can always submit for themselves
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if broadcaster allows public submissions
        if ($broadcaster->allowsPublicClipSubmissions()) {
            return true;
        }

        // Check if user has specific permission from broadcaster
        return $broadcaster->clipPermissionsGiven()
            ->where('can_submit_clips', true)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Check if user can edit clips for a specific broadcaster
     */
    public function canEditClipsFor(User $broadcaster): bool
    {
        // Broadcaster can always edit their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission from broadcaster
        return $broadcaster->clipPermissionsGiven()
            ->where('can_edit_clips', true)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Check if user can delete clips for a specific broadcaster
     */
    public function canDeleteClipsFor(User $broadcaster): bool
    {
        // Broadcaster can always delete their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission from broadcaster
        return $broadcaster->clipPermissionsGiven()
            ->where('can_delete_clips', true)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Check if user can moderate clips for a specific broadcaster
     */
    public function canModerateClipsFor(User $broadcaster): bool
    {
        // Broadcaster can always moderate their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission from broadcaster
        return $broadcaster->clipPermissionsGiven()
            ->where('can_moderate_clips', true)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Grant clip submission permission to a user
     */
    public function grantClipSubmissionPermission(User $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_submit_clips' => true]
        );
    }

    /**
     * Grant clip editing permission to a user
     */
    public function grantClipEditingPermission(User $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_edit_clips' => true]
        );
    }

    /**
     * Grant clip deletion permission to a user
     */
    public function grantClipDeletionPermission(User $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_delete_clips' => true]
        );
    }

    /**
     * Grant clip moderation permission to a user
     */
    public function grantClipModerationPermission(User $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_moderate_clips' => true]
        );
    }

    /**
     * Revoke clip submission permission from a user
     */
    public function revokeClipSubmissionPermission(User $user): void
    {
        $permission = $this->clipPermissionsGiven()->where('user_id', $user->id)->first();
        if ($permission) {
            $permission->update(['can_submit_clips' => false]);
        }
    }

    /**
     * Revoke clip editing permission from a user
     */
    public function revokeClipEditingPermission(User $user): void
    {
        $permission = $this->clipPermissionsGiven()->where('user_id', $user->id)->first();
        if ($permission) {
            $permission->update(['can_edit_clips' => false]);
        }
    }

    /**
     * Revoke clip deletion permission from a user
     */
    public function revokeClipDeletionPermission(User $user): void
    {
        $permission = $this->clipPermissionsGiven()->where('user_id', $user->id)->first();
        if ($permission) {
            $permission->update(['can_delete_clips' => false]);
        }
    }

    /**
     * Revoke clip moderation permission from a user
     */
    public function revokeClipModerationPermission(User $user): void
    {
        $permission = $this->clipPermissionsGiven()->where('user_id', $user->id)->first();
        if ($permission) {
            $permission->update(['can_moderate_clips' => false]);
        }
    }
}
