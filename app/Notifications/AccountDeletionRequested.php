<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletionRequested extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $deletionToken = $this->generateDeletionToken($notifiable);

        return (new MailMessage)
            ->subject('Account Deletion Request Confirmation')
            ->greeting('Hello '.$notifiable->twitch_display_name.',')
            ->line('We received your request to delete your account and all associated data.')
            ->line('This action cannot be undone and will permanently remove all your personal information in accordance with GDPR Article 17 (Right to be Forgotten).')
            ->line('Your clips will be anonymized but kept for platform integrity.')
            ->action('Confirm Account Deletion', url('/gdpr/confirm-deletion?token='.$deletionToken))
            ->line('If you did not request this deletion, please ignore this email.')
            ->line('This confirmation link will expire in 7 days.')
            ->salutation('Regards, ClipMook Team');
    }

    /**
     * Generate a secure deletion token
     */
    private function generateDeletionToken(User $user): string
    {
        return hash_hmac('sha256', $user->id.$user->email.'deletion'.now()->timestamp, config('app.key'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
