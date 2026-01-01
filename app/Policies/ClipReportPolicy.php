<?php

namespace App\Policies;

use App\Models\ClipReport;
use App\Models\User;

/**
 * Policy for controlling access to ClipReport resources.
 *
 * This policy implements authorization logic for report operations including
 * creating and viewing reports on clips.
 */
class ClipReportPolicy
{
    /**
     * Determine whether the user can view any reports.
     *
     * Only the user who created the report can view their own reports.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the report.
     */
    public function view(User $user, ClipReport $clipReport): bool
    {
        return $clipReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can create reports.
     *
     * All authenticated users can submit reports.
     * Email verification is not required as Twitch OAuth is trusted.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the report.
     *
     * Users cannot update reports after submission.
     */
    public function update(User $user, ClipReport $clipReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the report.
     *
     * Users can withdraw their own reports if still pending.
     */
    public function delete(User $user, ClipReport $clipReport): bool
    {
        return $clipReport->user_id === $user->id && $clipReport->status->isPending();
    }

    /**
     * Determine whether the user can restore the report.
     */
    public function restore(User $user, ClipReport $clipReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the report.
     */
    public function forceDelete(User $user, ClipReport $clipReport): bool
    {
        return false;
    }
}
