<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

use Illuminate\Support\Facades\Storage;

use function asset;
use function now;

/**
 * Handles user avatar management.
 */
trait HasAvatar
{
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->hasAvatar()) {
            return Storage::url($this->getAvatarStoragePath());
        }

        return asset('images/avatar-default.svg');
    }

    public function hasAvatar(): bool
    {
        return Storage::disk('public')->exists($this->getAvatarStoragePath());
    }

    public function deleteAvatar(): bool
    {
        if (Storage::disk('public')->exists($this->getAvatarStoragePath())) {
            return Storage::disk('public')->delete($this->getAvatarStoragePath());
        }

        return false;
    }

    public function getAvatarStoragePath(): string
    {
        return "avatars/users/{$this->id}.jpg";
    }
}
