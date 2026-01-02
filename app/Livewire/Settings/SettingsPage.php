<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Component;

use function request;
use function view;

final class SettingsPage extends Component
{
    public string $activeTab = 'account';

    public function mount(): void
    {
        $this->activeTab = request()->query('tab', 'account');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.settings.settings-page');
    }
}
