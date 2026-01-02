<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;

use function auth;
use function session;
use function view;

/**
 * User privacy settings component.
 */
final class PrivacySettings extends Component
{
    public User $user;

    public bool $profile_public = true;

    public bool $clips_public = true;

    public bool $stats_public = true;

    public bool $allow_contact = true;

    public bool $show_online_status = true;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->fill($this->user->privacy_settings ?? [
            'profile_public'     => true,
            'clips_public'       => true,
            'stats_public'       => true,
            'allow_contact'      => true,
            'show_online_status' => true,
        ]);
    }

    public function updatePrivacy(): void
    {
        $settings = [
            'profile_public'     => $this->profile_public,
            'clips_public'       => $this->clips_public,
            'stats_public'       => $this->stats_public,
            'allow_contact'      => $this->allow_contact,
            'show_online_status' => $this->show_online_status,
        ];

        $this->user->update([
            'privacy_settings' => $settings,
        ]);

        session()->flash('message', 'Privacy settings updated successfully.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.privacy-settings');
    }
}
