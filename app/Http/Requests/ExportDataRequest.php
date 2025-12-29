<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // No additional validation needed for export
        ];
    }
}
