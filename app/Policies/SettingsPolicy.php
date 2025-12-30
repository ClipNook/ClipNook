<?php

namespace App\Policies;

use App\Models\User;

class SettingsPolicy
{
    /**
     * Determine if the user can change settings.
     */
    public function change(User $user): bool
    {
        // Only logged-in users (with ID) can change settings
        return !is_null($user->id);
    }
}
