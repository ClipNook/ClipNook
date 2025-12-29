<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'confirm_name' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user     = $this->user();
            $expected = trim($user->display_name ?? '');

            if (trim($this->input('confirm_name', '')) !== $expected) {
                $validator->errors()->add('confirm_name', __('ui.delete_confirm_mismatch'));
            }
        });
    }
}
