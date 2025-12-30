<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTwitchClipId implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('twitch.validation_clip_id_string'));

            return;
        }

        // Twitch Clip IDs are typically 8-64 characters, alphanumeric with hyphens
        if (! preg_match('/^[A-Za-z0-9_-]{8,64}$/', $value)) {
            $fail(__('twitch.validation_clip_id_invalid'));
        }
    }
}
