<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOAuthState implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('twitch.validation_state_string'));

            return;
        }

        if (empty($value)) {
            $fail(__('twitch.validation_state_required'));

            return;
        }

        // Check if state matches stored OAuth state
        $storedState = session('oauth_state');
        if (! $storedState || ! hash_equals($storedState, $value)) {
            $fail(__('twitch.validation_state_csrf'));
        }

        // Clear the state after validation
        session()->forget('oauth_state');
    }
}
