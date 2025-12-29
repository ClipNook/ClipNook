<?php

namespace App\Models\Concerns;

trait HasAvatar
{
    /**
     * Get the user's avatar URL (Twitch, local or fallback SVG).
     */
    public function getAvatarUrlAttribute(): string
    {
        // If the user disabled avatars, always return fallback
        if ($this->isAvatarDisabled()) {
            return asset('images/avatar-default.svg');
        }

        // Check avatar source and return appropriate avatar
        switch ($this->avatar_source) {
            case 'custom':
                if (! empty($this->custom_avatar_path)) {
                    $disk = \Illuminate\Support\Facades\Storage::disk('public');
                    if ($disk->exists($this->custom_avatar_path)) {
                        return asset('storage/'.$this->custom_avatar_path);
                    }
                }
                break;

            case 'twitch':
            default:
                $avatar = $this->twitch_avatar;
                if (! empty($avatar)) {
                    // Check if it's a URL
                    if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                        return $avatar;
                    }

                    // Local file in public storage
                    $disk = \Illuminate\Support\Facades\Storage::disk('public');
                    if ($disk->exists($avatar)) {
                        return asset('storage/'.$avatar);
                    }
                }
                break;
        }

        // Fallback to our SVG
        return asset('images/avatar-default.svg');
    }

    /**
     * Whether the user has disabled avatars.
     *
     * Supports legacy boolean flag (avatar_disabled) and timestamp (avatar_disabled_at).
     */
    public function isAvatarDisabled(): bool
    {
        return (bool) $this->avatar_disabled || $this->avatar_disabled_at !== null;
    }

    /**
     * Enable user's avatar.
     */
    public function enableAvatar(): void
    {
        $this->avatar_disabled    = false;
        $this->avatar_disabled_at = null;
        $this->save();
    }

    /**
     * Disable user's avatar.
     */
    public function disableAvatar(): void
    {
        $this->avatar_disabled    = true;
        $this->avatar_disabled_at = now();
        $this->save();
    }

    /**
     * Deletes the user's avatar image (local files only).
     */
    public function deleteAvatar(): void
    {
        $avatar = $this->twitch_avatar;

        if (! empty($avatar)) {
            // Only delete if it's not a URL
            if (! filter_var($avatar, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($avatar);
            }
            $this->twitch_avatar = null;
            $this->save();
        }
    }

    /**
     * Check if user has a custom avatar set.
     */
    public function hasCustomAvatar(): bool
    {
        return ! empty($this->twitch_avatar) && ! $this->isAvatarDisabled();
    }
}
