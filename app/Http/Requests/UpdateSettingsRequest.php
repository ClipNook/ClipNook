<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
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

            // dialog action type (from JS) to map actions explicitly
            'actionType' => 'nullable|string',
        ];
    }
}
