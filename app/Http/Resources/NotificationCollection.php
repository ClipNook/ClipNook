<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @mixin \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification>
 */
class NotificationCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = NotificationResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total'        => $this->resource->count(),
                'unread_count' => $this->resource->whereNull('read_at')->count(),
                'read_count'   => $this->resource->whereNotNull('read_at')->count(),
            ],
            'links' => [
                'self'          => $request->url(),
                'mark_all_read' => route('notifications.mark-all-read'),
            ],
        ];
    }
}
