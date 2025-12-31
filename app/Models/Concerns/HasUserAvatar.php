<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait HasUserAvatar
{
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_disabled) {
            return null;
        }

        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            return Storage::url($this->custom_avatar_path);
        }

        return $this->twitch_avatar;
    }

    public function deleteAvatar(): void
    {
        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            Storage::delete($this->custom_avatar_path);
        }

        $this->update([
            'avatar_disabled_at' => now(),
            'avatar_disabled'    => true,
            'avatar_source'      => null,
            'custom_avatar_path' => null,
        ]);
    }
}
