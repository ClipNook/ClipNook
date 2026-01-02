<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Livewire\Settings\Concerns\ManagesUserSettings;
use Livewire\Component;

use function __;
use function view;

/**
 * User notification settings component.
 */
final class NotificationSettings extends Component
{
    use ManagesUserSettings;

    public bool $email_on_clip_approved = true;

    public bool $email_on_clip_rejected = true;

    public bool $email_on_new_comments = false;

    public bool $email_on_featured_clip = true;

    public bool $email_weekly_digest = true;

    public function mount(): void
    {
        $this->initializeUser();

        $defaults = [
            'email_on_clip_approved' => true,
            'email_on_clip_rejected' => true,
            'email_on_new_comments'  => false,
            'email_on_featured_clip' => true,
            'email_weekly_digest'    => true,
        ];

        $preferences = $this->loadSettings($defaults, 'notification_preferences');

        $this->fill($preferences);
    }

    public function updateNotifications(): void
    {
        $preferences = [
            'email_on_clip_approved' => $this->email_on_clip_approved,
            'email_on_clip_rejected' => $this->email_on_clip_rejected,
            'email_on_new_comments'  => $this->email_on_new_comments,
            'email_on_featured_clip' => $this->email_on_featured_clip,
            'email_weekly_digest'    => $this->email_weekly_digest,
        ];

        $this->saveSettings($preferences, 'notification_preferences');

        $this->notifySuccess(__('settings.notification_settings_updated'));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.notification-settings');
    }
}
