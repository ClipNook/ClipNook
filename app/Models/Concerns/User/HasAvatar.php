<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

use Illuminate\Support\Facades\Storage;

use function asset;

/**
 * Handles user avatar management.
 */
trait HasAvatar
{
    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            return Storage::url($this->custom_avatar_path);
        }

        if ($this->hasCustomAvatar()) {
            return $this->custom_avatar_path;
        }

        // Return default avatar if no other avatar is available
        return asset('images/avatar-default.svg');
    }

    /**
     * Check if user has a custom avatar.
     */
    public function hasCustomAvatar(): bool
    {
        return $this->custom_avatar_path && Storage::exists($this->custom_avatar_path);
    }

    /**
     * Delete custom avatar file.
     */
    public function deleteCustomAvatar(): bool
    {
        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            return Storage::delete($this->custom_avatar_path);
        }

        return false;
    }

    /**
     * Get avatar storage path for new uploads.
     */
    public function getAvatarStoragePath(): string
    {
        return "avatars/users/{$this->id}.jpg";
    }
}
