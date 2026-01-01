<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a clip is moderated.
 *
 * This event is dispatched whenever a moderator approves, rejects, or flags a clip.
 * It includes the action taken and the moderator who performed it.
 */
class ClipModerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Clip $clip,
        public User $moderator,
        public string $action,
        public ?string $reason = null
    ) {}
}
