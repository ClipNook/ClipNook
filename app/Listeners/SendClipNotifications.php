<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ClipModerated;
use App\Events\ClipSubmitted;
use App\Notifications\ClipApproved;
use App\Notifications\ClipRejected;
use App\Notifications\ClipSubmitted as ClipSubmittedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Listener for sending notifications related to clip activities.
 *
 * This listener handles sending notifications to users when clips are
 * submitted or moderated, keeping users informed about their content.
 */
class SendClipNotifications
{
    /**
     * Handle the ClipSubmitted event.
     */
    public function handleClipSubmitted(ClipSubmitted $event): void
    {
        // Notify moderators who can moderate clips for this broadcaster
        $moderators = $event->clip->broadcaster->clipPermissionsReceived()
            ->where('can_moderate_clips', true)
            ->with('user')
            ->get()
            ->pluck('user')
            ->push($event->clip->broadcaster) // Also notify the broadcaster
            ->unique('id');

        Notification::send($moderators, new ClipSubmittedNotification($event->clip));
    }

    /**
     * Handle the ClipModerated event.
     */
    public function handleClipModerated(ClipModerated $event): void
    {
        // Notify the submitter based on the moderation action
        switch ($event->action) {
            case 'approve':
                $event->clip->submitter->notify(new ClipApproved($event->clip));
                break;
            case 'reject':
                $event->clip->submitter->notify(new ClipRejected($event->clip, $event->reason));
                break;
                // For 'flag', we might not notify immediately to avoid spam
        }
    }
}
