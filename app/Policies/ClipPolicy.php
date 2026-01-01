<?php

namespace App\Policies;

use App\Models\Clip;
use App\Models\User;

/**
 * Policy for controlling access to Clip resources.
 *
 * This policy implements authorization logic for clip operations including
 * viewing, creating, updating, deleting, and moderating clips. It uses
 * the granular permission system to determine user capabilities.
 */
class ClipPolicy
{
    /**
     * Determine whether the user can view any clips.
     *
     * @param  User  $user  The authenticated user
     * @return bool True if user can view clips
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view clips (with appropriate filtering)
    }

    /**
     * Determine whether the user can view a specific clip.
     *
     * Users can view their own submitted clips or any approved clips.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being viewed
     * @return bool True if user can view this clip
     */
    public function view(User $user, Clip $clip): bool
    {
        // Users can view their own clips or approved clips
        return $clip->isSubmittedBy($user) || $clip->isApproved();
    }

    /**
     * Determine whether the user can create new clips.
     *
     * All authenticated users can submit clips.
     * Email verification is not required as Twitch OAuth is trusted.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update a specific clip.
     *
     * Users can update clips they have permission to moderate.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being updated
     * @return bool True if user can update this clip
     */
    public function update(User $user, Clip $clip): bool
    {
        // Moderators and broadcasters can update clips
        return $clip->canBeModeratedBy($user);
    }

    /**
     * Determine whether the user can delete a specific clip.
     *
     * Users can delete clips they have permission to delete.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being deleted
     * @return bool True if user can delete this clip
     */
    public function delete(User $user, Clip $clip): bool
    {
        // Users can delete their own clips, admins can delete any clip
        return $clip->canBeDeletedBy($user);
    }

    /**
     * Determine whether the user can moderate a specific clip.
     *
     * Moderation includes approving, rejecting, or flagging clips.
     * Admins and moderators can moderate any clip.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being moderated
     * @return bool True if user can moderate this clip
     */
    public function moderate(User $user, Clip $clip): bool
    {
        // Admins and moderators can moderate any clip
        if ($user->isStaff()) {
            return true;
        }

        return $clip->canBeModeratedBy($user);
    }

    /**
     * Determine whether the user can restore a soft-deleted clip.
     *
     * Currently not implemented - clips are not soft-deleted.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being restored
     * @return bool Always false (not implemented)
     */
    public function restore(User $user, Clip $clip): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete a clip.
     *
     * Currently not implemented - permanent deletion not allowed.
     *
     * @param  User  $user  The authenticated user
     * @param  Clip  $clip  The clip being permanently deleted
     * @return bool Always false (not implemented)
     */
    public function forceDelete(User $user, Clip $clip): bool
    {
        return false;
    }
}
