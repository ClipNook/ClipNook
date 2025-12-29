<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CutterProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'hourly_rate'      => $this->hourly_rate,
            'response_time'    => $this->response_time,
            'skills'           => $this->skills,
            'is_available'     => $this->is_available,
            'portfolio_url'    => $this->portfolio_url,
            'experience_years' => $this->experience_years,
            // Relationship
            'user' => UserResource::make($this->whenLoaded('user')),
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
