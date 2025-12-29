<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'confirm_name' => [
                'required',
                Rule::in([$this->user()->display_name]),
            ],
            'password' => ['required', 'current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_name.required'     => __('ui.validation.confirm_name_required'),
            'confirm_name.in'           => __('ui.validation.confirm_name_mismatch'),
            'password.required'         => __('ui.validation.password_required'),
            'password.current_password' => __('ui.validation.password_incorrect'),
        ];
    }
}