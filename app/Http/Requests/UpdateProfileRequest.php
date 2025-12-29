<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Only authenticated users can update their profile.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'intro'               => ['nullable', 'string', 'max:1000'],
            'available_for_jobs'  => ['boolean'],
            'allow_clip_sharing'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'intro.max'               => __('ui.validation.intro_max'),
        ];
    }
}
