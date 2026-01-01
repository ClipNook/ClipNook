<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

/**
 * Handles user role management and permissions.
 */
trait HasRoles
{
    /**
     * Check if user is a viewer.
     */
    public function isViewer(): bool
    {
        return $this->is_viewer;
    }

    /**
     * Check if user is a cutter.
     */
    public function isCutter(): bool
    {
        return $this->is_cutter;
    }

    /**
     * Check if user is a streamer.
     */
    public function isStreamer(): bool
    {
        return $this->is_streamer;
    }

    /**
     * Check if user is a moderator.
     */
    public function isModerator(): bool
    {
        return $this->is_moderator;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if user has any staff role (moderator or admin).
     */
    public function isStaff(): bool
    {
        return $this->is_moderator || $this->is_admin;
    }

    /**
     * Get user's role badges for UI display.
     */
    public function getRoleBadgesAttribute(): array
    {
        $badges = [];

        if ($this->is_admin) {
            $badges[] = ['type' => 'admin', 'label' => 'Admin', 'color' => 'danger'];
        }

        if ($this->is_moderator) {
            $badges[] = ['type' => 'moderator', 'label' => 'Moderator', 'color' => 'warning'];
        }

        if ($this->is_streamer) {
            $badges[] = ['type' => 'streamer', 'label' => 'Streamer', 'color' => 'primary'];
        }

        if ($this->is_cutter) {
            $badges[] = ['type' => 'cutter', 'label' => 'Cutter', 'color' => 'info'];
        }

        return $badges;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $role): bool
    {
        $roleField = "is_{$role}";

        if (! in_array($roleField, ['is_viewer', 'is_cutter', 'is_streamer', 'is_moderator', 'is_admin'])) {
            return false;
        }

        $this->update([$roleField => true]);

        return true;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string $role): bool
    {
        $roleField = "is_{$role}";

        if (! in_array($roleField, ['is_viewer', 'is_cutter', 'is_streamer', 'is_moderator', 'is_admin'])) {
            return false;
        }

        $this->update([$roleField => false]);

        return true;
    }

    /**
     * Get user's primary role for display.
     */
    public function getPrimaryRoleAttribute(): string
    {
        if ($this->is_admin) {
            return 'Admin';
        }

        if ($this->is_moderator) {
            return 'Moderator';
        }

        if ($this->is_streamer) {
            return 'Streamer';
        }

        if ($this->is_cutter) {
            return 'Cutter';
        }

        return 'Viewer';
    }
}
