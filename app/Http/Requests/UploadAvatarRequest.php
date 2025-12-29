<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120', // 5MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000,ratio=1/1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required'   => __('ui.validation.avatar_required'),
            'avatar.image'      => __('ui.validation.avatar_image'),
            'avatar.mimes'      => __('ui.validation.avatar_mimes'),
            'avatar.max'        => __('ui.validation.avatar_max_size'),
            'avatar.dimensions' => __('ui.validation.avatar_dimensions'),
        ];
    }
}
