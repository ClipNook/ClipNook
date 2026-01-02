<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Clip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use function url;

/**
 * Notification sent to users when their clip is rejected.
 *
 * This notification informs submitters that their clip has been rejected,
 * including the reason for rejection to help them improve future submissions.
 */
final class ClipRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Clip $clip,
        public string $reason,
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
        return (new MailMessage())
            ->subject("Your clip was not approved: {$this->clip->title}")
            ->greeting("Hello {$notifiable->twitch_display_name},")
            ->line("Unfortunately, your submitted clip '{$this->clip->title}' was not approved.")
            ->line("**Reason:** {$this->reason}")
            ->line("**Broadcaster:** {$this->clip->broadcaster->twitch_display_name}")
            ->line('You can submit another clip anytime. Please review the community guidelines.')
            ->action('Submit Another Clip', url('/submit'))
            ->line('Thank you for your understanding.');
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
            'reason'           => $this->reason,
            'broadcaster_name' => $this->clip->broadcaster->twitch_display_name,
            'type'             => 'clip_rejected',
        ];
    }
}
