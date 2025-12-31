<?php

namespace App\Events;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Real-time clip status update event
 */
class ClipStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Clip $clip,
        public string $oldStatus,
        public string $newStatus,
        public ?User $moderator = null,
    ) {}

    /**
     * Get the channels the event should broadcast on
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("clip.{$this->clip->id}"),
            new PrivateChannel("user.{$this->clip->submitter_id}"),
        ];
    }

    /**
     * Get the data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'clip_id'      => $this->clip->id,
            'old_status'   => $this->oldStatus,
            'new_status'   => $this->newStatus,
            'moderated_by' => $this->moderator?->twitch_display_name,
            'timestamp'    => now()->toISOString(),
        ];
    }
}
