<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Notification
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'type'      => $this->type,
            'title'     => $this->title,
            'message'   => $this->message,
            'url'       => $this->url,
            'data'      => $this->data,
            'read_at'   => $this->read_at,
            'is_read'   => $this->isRead(),
            'is_unread' => $this->isUnread(),
            // Timestamps
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
            // Relationships
            'user'       => UserResource::make($this->whenLoaded('user')),
            'notifiable' => $this->whenLoaded('notifiable'),
        ];
    }
}
