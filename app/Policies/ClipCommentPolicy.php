<?php

namespace App\Policies;

use App\Models\ClipComment;
use App\Models\User;

/**
 * Policy for controlling access to ClipComment resources.
 *
 * This policy implements authorization logic for comment operations including
 * viewing, creating, updating, and deleting comments on clips.
 */
class ClipCommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(User $user, ClipComment $clipComment): bool
    {
        return ! $clipComment->is_deleted || $clipComment->user_id === $user->id;
    }

    /**
     * Determine whether the user can create comments.
     *
     * Only verified users can post comments to prevent spam.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the comment.
     *
     * Users can only edit their own comments within a reasonable timeframe.
     */
    public function update(User $user, ClipComment $clipComment): bool
    {
        if ($clipComment->user_id !== $user->id) {
            return false;
        }

        // Allow editing within 15 minutes of posting
        return $clipComment->created_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * Users can delete their own comments.
     */
    public function delete(User $user, ClipComment $clipComment): bool
    {
        return $clipComment->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, ClipComment $clipComment): bool
    {
        return $clipComment->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, ClipComment $clipComment): bool
    {
        return $clipComment->user_id === $user->id;
    }
}
