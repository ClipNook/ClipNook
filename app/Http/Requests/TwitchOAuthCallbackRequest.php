<?php

namespace App\Http\Requests;

use App\Rules\ValidOAuthState;
use Illuminate\Foundation\Http\FormRequest;

class TwitchOAuthCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all, but validate CSRF via state
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code'  => 'required|string|min:10|max:100',
            'state' => ['required', 'string', 'size:40', new ValidOAuthState],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required'  => __('twitch.validation_code_required'),
            'code.string'    => __('twitch.validation_code_string'),
            'code.min'       => __('twitch.validation_code_min'),
            'code.max'       => __('twitch.validation_code_max'),
            'state.required' => __('twitch.validation_state_required'),
            'state.string'   => __('twitch.validation_state_string'),
            'state.size'     => __('twitch.validation_state_size'),
        ];
    }
}
