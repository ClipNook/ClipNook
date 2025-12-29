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
        return [
            'twitch_display_name' => ['nullable', 'string', 'max:255'],
            'twitch_email'        => ['required', 'email:rfc,dns', 'unique:users,twitch_email,'.$this->user()->id],
            'intro'               => ['nullable', 'string', 'max:1000'],
            'available_for_jobs'  => ['boolean'],
            'allow_clip_sharing'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'twitch_display_name.max' => __('ui.validation.display_name_max'),
            'twitch_email.required'   => __('ui.validation.email_required'),
            'twitch_email.email'      => __('ui.validation.email_invalid'),
            'twitch_email.unique'     => __('ui.validation.email_taken'),
            'intro.max'               => __('ui.validation.intro_max'),
        ];
    }
}
