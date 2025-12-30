<?php

namespace App\Policies;

use App\Models\Clip;
use App\Models\User;

class ClipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view clips (with appropriate filtering)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Clip $clip): bool
    {
        // Users can view their own clips or approved clips
        return $clip->user_id === $user->id || $clip->isApproved();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail(); // Only verified users can submit clips
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Clip $clip): bool
    {
        // Only moderators can update clips (for moderation)
        return $user->can('moderate clips');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Clip $clip): bool
    {
        // Users can delete their own clips, moderators can delete any
        return $clip->user_id === $user->id || $user->can('moderate clips');
    }

    /**
     * Determine whether the user can moderate clips.
     */
    public function moderate(User $user, Clip $clip): bool
    {
        return $user->can('moderate clips');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Clip $clip): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Clip $clip): bool
    {
        return false;
    }
}
