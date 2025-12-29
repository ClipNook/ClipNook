<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportDataRequest extends FormRequest
{
    /**
     * Only authenticated users can export their data.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // No additional validation needed for export
        ];
    }
}
