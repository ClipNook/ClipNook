<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a clip is submitted.
 *
 * This event is dispatched whenever a user submits a new clip for moderation.
 * It can be used for logging, notifications, or triggering background processes.
 */
class ClipSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Clip $clip,
        public User $submitter
    ) {}
}
