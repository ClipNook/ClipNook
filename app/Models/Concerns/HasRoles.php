<?php

namespace App\Models\Concerns;

trait HasRoles
{
    /**
     * Check if user has viewer role.
     */
    public function isViewer(): bool
    {
        return (bool) $this->is_viewer;
    }

    /**
     * Check if user has cutter role.
     */
    public function isCutter(): bool
    {
        return (bool) $this->is_cutter;
    }

    /**
     * Check if user has streamer role.
     */
    public function isStreamer(): bool
    {
        return (bool) $this->is_streamer;
    }

    /**
     * Check if user has moderator role.
     */
    public function isModerator(): bool
    {
        return (bool) $this->is_moderator;
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Check if user has any role assigned.
     */
    public function hasAnyRole(): bool
    {
        return $this->isStreamer() || $this->isCutter() || $this->isModerator() || $this->isAdmin();
    }

    /**
     * Get user's primary role for display.
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        if ($this->isAdmin()) {
            return 'admin';
        }

        if ($this->isModerator()) {
            return 'moderator';
        }

        if ($this->isStreamer()) {
            return 'streamer';
        }

        if ($this->isCutter()) {
            return 'cutter';
        }

        return null;
    }
}
