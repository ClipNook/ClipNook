<?php

declare(strict_types=1);

namespace App\Http\Requests\Clip;

use Illuminate\Foundation\Http\FormRequest;

use function strtolower;

final class UpdateClipRequest extends FormRequest
{
    /**
     * Form request for updating/moderating a clip.
     * Handles validation for clip moderation actions including approval,
     * rejection, flagging, and featured status changes.
     */
    public function authorize(): bool
    {
        $clip = $this->route('clip');

        return $clip && $this->user() && $this->user()->can('moderate', $clip);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:approve,reject,flag,toggle_featured',
            ],
            'reason' => [
                'required_if:action,reject,flag',
                'string',
                'max:1000',
                'nullable',
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
            'action.required'    => 'An action is required.',
            'action.in'          => 'The selected action is invalid.',
            'reason.required_if' => 'A reason is required for rejection or flagging.',
            'reason.max'         => 'The reason cannot exceed 1000 characters.',
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
            'action' => 'moderation action',
            'reason' => 'moderation reason',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('action')) {
            $this->merge([
                'action' => strtolower($this->input('action')),
            ]);
        }
    }
}
