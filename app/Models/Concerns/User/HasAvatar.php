<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

use Illuminate\Support\Facades\Storage;

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
        if ($this->avatar_disabled) {
            return null;
        }

        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            return Storage::url($this->custom_avatar_path);
        }

        if ($this->twitch_avatar) {
            return $this->twitch_avatar;
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
     * Disable avatar.
     */
    public function disableAvatar(): void
    {
        $this->update([
            'avatar_disabled'    => true,
            'avatar_disabled_at' => now(),
        ]);
    }

    /**
     * Enable avatar.
     */
    public function enableAvatar(): void
    {
        $this->update([
            'avatar_disabled'    => false,
            'avatar_disabled_at' => null,
        ]);
    }

    /**
     * Get avatar storage path for new uploads.
     */
    public function getAvatarStoragePath(): string
    {
        return "avatars/users/{$this->id}.jpg";
    }
}
