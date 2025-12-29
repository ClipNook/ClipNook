<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // Only allow editing of profile free-text fields via this request.
        return [
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'display_name.required' => __('ui.validation.display_name_required'),
            'display_name.max'      => __('ui.validation.display_name_max'),
            'email.required'        => __('ui.validation.email_required'),
            'email.unique'          => __('ui.validation.email_taken'),
            'username.unique'       => __('ui.validation.username_taken'),
            'username.alpha_dash'   => __('ui.validation.username_alpha_dash'),
            'bio.max'               => __('ui.validation.bio_max'),
        ];
    }
}
