<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasUserRoles
{
    public function hasRole(string $role): bool
    {
        return $this->{'is_'.$role} ?? false;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isStaff(): bool
    {
        return $this->is_admin || $this->is_moderator;
    }

    public function getPrimaryRoleAttribute(): string
    {
        if ($this->is_admin) {
            return 'admin';
        }
        if ($this->is_moderator) {
            return 'moderator';
        }
        if ($this->is_streamer) {
            return 'streamer';
        }
        if ($this->is_cutter) {
            return 'cutter';
        }

        return 'viewer';
    }

    public function getRoleColorAttribute(): string
    {
        return match ($this->primary_role) {
            'admin'     => 'danger',
            'moderator' => 'warning',
            'streamer'  => 'success',
            'cutter'    => 'info',
            default     => 'secondary',
        };
    }

    public function getRoleBadgesAttribute(): array
    {
        $badges = [];
        if ($this->is_admin) {
            $badges[] = ['name' => 'Admin', 'color' => 'danger'];
        }
        if ($this->is_moderator) {
            $badges[] = ['name' => 'Moderator', 'color' => 'warning'];
        }
        if ($this->is_streamer) {
            $badges[] = ['name' => 'Streamer', 'color' => 'success'];
        }
        if ($this->is_cutter) {
            $badges[] = ['name' => 'Cutter', 'color' => 'info'];
        }
        if ($this->is_viewer) {
            $badges[] = ['name' => 'Viewer', 'color' => 'secondary'];
        }

        return $badges;
    }
}
