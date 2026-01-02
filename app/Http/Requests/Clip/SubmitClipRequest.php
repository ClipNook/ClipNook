<?php

declare(strict_types=1);

namespace App\Http\Requests\Clip;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for submitting a new clip.
 *
 * Handles validation for clip submission including Twitch clip ID validation
 * and permission checks.
 */
final class SubmitClipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'twitch_clip_id' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9_-]+$/',
                'max:255',
                // Custom rule to check if clip already exists
                static function ($attribute, $value, $fail): void {
                    if (\App\Models\Clip::where('twitch_clip_id', $value)->exists()) {
                        $fail('This clip has already been submitted.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'twitch_clip_id.required' => 'A Twitch clip ID is required.',
            'twitch_clip_id.regex'    => 'The Twitch clip ID format is invalid.',
            'twitch_clip_id.max'      => 'The Twitch clip ID is too long.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'twitch_clip_id' => 'Twitch clip ID',
        ];
    }
}
