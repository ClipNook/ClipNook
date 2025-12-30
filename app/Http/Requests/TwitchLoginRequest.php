<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwitchLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'scopes'            => 'nullable|array',
            'scopes.*'          => 'string|in:user:read:email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'custom_avatar.image' => 'The uploaded file must be an image.',
            'custom_avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif.',
            'custom_avatar.max'   => 'The avatar may not be greater than 5MB.',
            'scopes.*.in'         => 'Invalid scope selected.',
        ];
    }

    /**
     * Get the validated data with defaults.
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();

        return [
            'scopes' => ['user:read:email'],
        ];
    }
}
