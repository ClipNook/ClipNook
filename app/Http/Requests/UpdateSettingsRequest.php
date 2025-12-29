<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Only authenticated users can update their settings.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Avatar actions are optional flags
            'remove_avatar'  => 'nullable|boolean',
            'restore_avatar' => 'nullable|boolean',

            // Role/profile updates
            'is_streamer'        => 'nullable|boolean',
            'is_cutter'          => 'nullable|boolean',
            'intro'              => 'nullable|string|max:2000',
            'available_for_jobs' => 'nullable|boolean',

            // Preferences (Laravel 12 / PHP 8.5)
            'theme_preference' => ['nullable', 'string', Rule::in(['light', 'dark', 'system'])],
            'locale'           => ['nullable', 'string', Rule::in(['en', 'de', 'fr', 'es', 'it'])],
            'timezone'         => ['nullable', 'timezone'],

            // Accent color (predefined keys from color-picker)
            'accent_color' => ['nullable', 'string', Rule::in(['purple', 'blue', 'green', 'red', 'orange', 'pink', 'indigo', 'teal', 'amber', 'slate'])],

            // dialog action type (from JS) to map actions explicitly
            'actionType' => 'nullable|string',
        ];
    }
}
