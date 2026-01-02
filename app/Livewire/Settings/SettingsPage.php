<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Component;

use function in_array;
use function request;
use function view;

final class SettingsPage extends Component
{
    private const ALLOWED_TABS = [
        'account',
        'profile',
        'streamer',
        'privacy',
        'appearance',
        'notifications',
        'sessions',
    ];

    public string $activeTab = 'account';

    public function mount(): void
    {
        $requestedTab    = request()->query('tab', 'account');
        $this->activeTab = in_array($requestedTab, self::ALLOWED_TABS, true)
            ? $requestedTab
            : 'account';
    }

    public function setActiveTab(string $tab): void
    {
        if (! in_array($tab, self::ALLOWED_TABS, true)) {
            return;
        }

        $this->activeTab = $tab;

        // Update URL without page reload
        $this->dispatch('update-query-string', tab: $tab);
    }

    #[Layout('components.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.settings-page');
    }
}
