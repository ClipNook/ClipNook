<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

use function auth;
use function session;
use function view;

/**
 * User security settings component.
 */
final class SecuritySettings extends Component
{
    public User $user;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $two_factor_enabled = false;

    public function mount(): void
    {
        $this->user               = auth()->user();
        $this->two_factor_enabled = $this->user->two_factor_secret !== null;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if (! Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');

            return;
        }

        $this->user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('message', 'Password updated successfully.');
    }

    public function toggleTwoFactor(): void
    {
        if ($this->two_factor_enabled) {
            $this->user->update([
                'two_factor_secret'         => null,
                'two_factor_recovery_codes' => null,
            ]);
            $this->two_factor_enabled = false;
            session()->flash('message', 'Two-factor authentication disabled.');
        } else {
            // Generate 2FA secret and show QR code
            // This would typically use a package like laravel/fortify
            session()->flash('message', 'Two-factor authentication setup initiated.');
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.security-settings');
    }
}
