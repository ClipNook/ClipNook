<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use function url;

final class AdminAlert extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $level = 'info',
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->notifications_email) {
            $channels[] = 'mail';
        }

        if ($notifiable->notifications_web) {
            $channels[] = 'database';
        }

        if ($notifiable->notifications_ntfy && $notifiable->ntfy_server_url && $notifiable->ntfy_topic) {
            $channels[] = 'ntfy';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title)
            ->line($this->message)
            ->action('View Dashboard', url('/admin'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'level'   => $this->level,
        ];
    }

    public function toNtfy(object $notifiable): array
    {
        return [
            'topic'    => $notifiable->ntfy_topic,
            'title'    => $this->title,
            'message'  => $this->message,
            'priority' => match ($this->level) {
                'info'     => 3,
                'warning'  => 4,
                'critical' => 5,
                default    => 3,
            },
        ];
    }
}
