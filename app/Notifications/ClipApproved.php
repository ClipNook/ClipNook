<?php

namespace App\Notifications;

use App\Models\Clip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to users when their clip is approved.
 *
 * This notification informs submitters that their clip has been approved
 * and is now publicly visible.
 */
class ClipApproved extends Notification implements ShouldQueue
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
            ->subject("Your clip has been approved: {$this->clip->title}")
            ->greeting("Great news, {$notifiable->twitch_display_name}!")
            ->line("Your submitted clip '{$this->clip->title}' has been approved and is now live!")
            ->line("**Broadcaster:** {$this->clip->broadcaster->twitch_display_name}")
            ->action('View Clip', url("/clips/{$this->clip->id}"))
            ->line('Thank you for contributing to the community!');
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
            'broadcaster_name' => $this->clip->broadcaster->twitch_display_name,
            'type'             => 'clip_approved',
        ];
    }
}
