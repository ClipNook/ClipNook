<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;

use function auth;
use function session;
use function view;

/**
 * User profile settings component.
 */
final class ProfileSettings extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public bool $email_verified = false;

    public ?string $bio = null;

    public ?string $website = null;

    public ?string $location = null;

    public bool $is_private = false;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->fill($this->user->only([
            'name', 'email', 'bio', 'website', 'location', 'is_private',
        ]));
        $this->email_verified = $this->user->hasVerifiedEmail();
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,'.$this->user->id,
            'bio'      => 'nullable|string|max:500',
            'website'  => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $this->user->update([
            'name'       => $this->name,
            'email'      => $this->email,
            'bio'        => $this->bio,
            'website'    => $this->website,
            'location'   => $this->location,
            'is_private' => $this->is_private,
        ]);

        session()->flash('message', 'Profile updated successfully.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.profile-settings');
    }
}
