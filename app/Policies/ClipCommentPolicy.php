<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ClipComment;
use App\Models\User;

use function config;
use function now;

/**
 * Policy for controlling access to ClipComment resources.
 *
 * This policy implements authorization logic for comment operations including
 * viewing, creating, updating, and deleting comments on clips.
 */
final class ClipCommentPolicy
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
     * All authenticated users can post comments.
     * Email verification is not required as Twitch OAuth is trusted.
     */
    public function create(User $user): bool
    {
        return true;
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
        return $clipComment->created_at->diffInMinutes(now()) <= config('constants.time.comment_edit_minutes');
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
