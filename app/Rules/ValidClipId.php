<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule for Twitch Clip IDs.
 *
 * Validates both plain clip IDs and full Twitch URLs,
 * extracting the ID when necessary and ensuring proper format.
 */
class ValidClipId implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('clips.validation_clip_id_string'));

            return;
        }

        $clipId = $this->extractClipId($value);

        if (! $clipId) {
            $fail(__('clips.validation_clip_id_format'));

            return;
        }

        if (! $this->isValidClipIdFormat($clipId)) {
            $fail(__('clips.validation_clip_id_format'));

            return;
        }

        if (strlen($clipId) < config('constants.limits.clip_id_min_length') || strlen($clipId) > config('constants.limits.clip_id_max_length')) {
            $fail(__('clips.validation_clip_id_min'));

            return;
        }
    }

    /**
     * Extract clip ID from URL or return the ID itself.
     */
    protected function extractClipId(string $value): ?string
    {
        // If it's a URL, extract the clip ID
        if (str_contains($value, 'twitch.tv') || str_contains($value, 'clips.twitch.tv')) {
            // Pattern: https://clips.twitch.tv/ClipID or https://twitch.tv/.../clip/ClipID
            if (preg_match('/clips\.twitch\.tv\/([a-zA-Z0-9_-]+)/', $value, $matches)) {
                return $matches[1];
            }

            if (preg_match('/\/clip\/([a-zA-Z0-9_-]+)/', $value, $matches)) {
                return $matches[1];
            }

            return null;
        }

        // Otherwise, assume it's already a clip ID
        return $value;
    }

    /**
     * Check if the clip ID matches the expected format.
     */
    protected function isValidClipIdFormat(string $clipId): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_-]+$/', $clipId);
    }
}
