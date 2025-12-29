<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolesRequest extends FormRequest
{
    /**
     * Only authenticated users can update their roles.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'is_streamer'        => ['boolean'],
            'is_cutter'          => ['boolean'],
            'intro'              => ['nullable', 'string', 'max:500'],
            'available_for_jobs' => ['boolean'],
            'allow_clip_sharing' => ['boolean'],
            'hourly_rate'        => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'response_time'      => ['nullable', 'string', 'in:24,48,72,168'],
            'skills'             => ['nullable', 'string'], // JSON string
            'portfolio_url'      => ['nullable', 'url', 'max:255'],
            'experience_years'   => ['nullable', 'integer', 'min:0', 'max:50'],
            'stream_schedule'    => ['nullable', 'string', 'max:255'],
            'preferred_games'    => ['nullable', 'string', 'max:255'],
            'stream_quality'     => ['nullable', 'string', 'in:480p,720p,1080p,1440p,4k'],
            'has_overlay'        => ['boolean'],
        ];
    }
}
