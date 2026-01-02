<?php

declare(strict_types=1);

namespace App\Models\Concerns\Clip;

use Illuminate\Support\Facades\Storage;

use function asset;

/**
 * Handles clip media management (thumbnails, etc.).
 */
trait HasMedia
{
    /**
     * Get the thumbnail URL (local or remote).
     */
    public function getThumbnailUrlAttribute(): string|bool
    {
        // Use local thumbnail if available, otherwise fall back to Twitch URL
        if ($this->local_thumbnail_path && Storage::disk('public')->exists($this->local_thumbnail_path)) {
            return asset('storage/'.$this->local_thumbnail_path);
        }

        return false;
    }

    /**
     * Check if local thumbnail exists.
     */
    public function hasLocalThumbnail(): bool
    {
        return $this->local_thumbnail_path && Storage::disk('public')->exists($this->local_thumbnail_path);
    }

    /**
     * Delete local thumbnail file.
     */
    public function deleteLocalThumbnail(): bool
    {
        if ($this->local_thumbnail_path && Storage::disk('public')->exists($this->local_thumbnail_path)) {
            return Storage::disk('public')->delete($this->local_thumbnail_path);
        }

        return false;
    }

    /**
     * Get thumbnail storage path for new uploads.
     */
    public function getThumbnailStoragePath(): string
    {
        return "thumbnails/clips/{$this->uuid}.jpg";
    }
}
