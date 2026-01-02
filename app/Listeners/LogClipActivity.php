<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ClipModerated;
use App\Events\ClipSubmitted;
use Illuminate\Support\Facades\Log;

/**
 * Listener for logging clip-related activities.
 *
 * This listener handles logging for clip submissions and moderations
 * to provide audit trails for administrative purposes.
 */
final class LogClipActivity
{
    /**
     * Handle the ClipSubmitted event.
     */
    public function handleClipSubmitted(ClipSubmitted $event): void
    {
        Log::info('Clip submitted for moderation', [
            'clip_id'        => $event->clip->id,
            'twitch_clip_id' => $event->clip->twitch_clip_id,
            'submitter_id'   => $event->submitter->id,
            'broadcaster_id' => $event->clip->broadcaster_id,
            'title'          => $event->clip->title,
        ]);
    }

    /**
     * Handle the ClipModerated event.
     */
    public function handleClipModerated(ClipModerated $event): void
    {
        Log::info('Clip moderated', [
            'clip_id'        => $event->clip->id,
            'twitch_clip_id' => $event->clip->twitch_clip_id,
            'moderator_id'   => $event->moderator->id,
            'action'         => $event->action,
            'reason'         => $event->reason,
            'new_status'     => $event->clip->status,
        ]);
    }
}
