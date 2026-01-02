<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

use function rtrim;

final class NtfyChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable->notifications_ntfy
            || ! $notifiable->ntfy_server_url
            || ! $notifiable->ntfy_topic) {
            return;
        }

        $data = $notification->toNtfy($notifiable);

        $url = rtrim($notifiable->ntfy_server_url, '/').'/'.$notifiable->ntfy_topic;

        Http::withHeaders([
            'Authorization' => $notifiable->ntfy_auth_token ? 'Bearer '.$notifiable->ntfy_auth_token : null,
        ])->post($url, $data);
    }
}
