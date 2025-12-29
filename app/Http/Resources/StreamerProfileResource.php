<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\StreamerProfile
 */
class StreamerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'intro'           => $this->intro,
            'stream_schedule' => $this->stream_schedule,
            'preferred_games' => $this->preferred_games,
            'stream_quality'  => $this->stream_quality,
            'has_overlay'     => $this->has_overlay,
            // Relationship
            'user' => UserResource::make($this->whenLoaded('user')),
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
