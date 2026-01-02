<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidClipId;
use Illuminate\Foundation\Http\FormRequest;

use function __;
use function preg_match;
use function str_contains;

final class SubmitClipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'twitch_clip_id' => ['required', 'string', new ValidClipId()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'twitch_clip_id.required' => __('clips.validation_clip_id_required'),
            'twitch_clip_id.string'   => __('clips.validation_clip_id_string'),
        ];
    }

    /**
     * Get the validated Twitch clip ID.
     */
    public function getClipId(): string
    {
        $value = $this->validated('twitch_clip_id');

        return $this->extractClipIdFromValue($value);
    }

    /**
     * Extract clip ID from URL or return the value.
     */
    protected function extractClipIdFromValue(string $value): string
    {
        if (str_contains($value, 'twitch.tv')) {
            if (preg_match('/clips\.twitch\.tv\/([a-zA-Z0-9_-]+)/', $value, $matches)) {
                return $matches[1];
            }

            if (preg_match('/\/clip\/([a-zA-Z0-9_-]+)/', $value, $matches)) {
                return $matches[1];
            }
        }

        return $value;
    }
}
