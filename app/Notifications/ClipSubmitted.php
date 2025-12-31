<?php

namespace App\Notifications;

use App\Models\Clip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to moderators when a new clip is submitted.
 *
 * This notification informs moderators about new clips that need review,
 * helping them stay on top of pending moderations.
 */
class ClipSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Clip $clip
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New clip submitted: {$this->clip->title}")
            ->greeting("Hello {$notifiable->twitch_display_name}!")
            ->line('A new clip has been submitted and needs your review.')
            ->line("**Clip:** {$this->clip->title}")
            ->line("**Submitted by:** {$this->clip->submitter->twitch_display_name}")
            ->line("**Broadcaster:** {$this->clip->broadcaster->twitch_display_name}")
            ->action('Review Clip', url("/admin/clips/{$this->clip->id}"))
            ->line('Please moderate this clip as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'clip_id'          => $this->clip->id,
            'title'            => $this->clip->title,
            'submitter_name'   => $this->clip->submitter->twitch_display_name,
            'broadcaster_name' => $this->clip->broadcaster->twitch_display_name,
            'type'             => 'clip_submitted',
        ];
    }
}
