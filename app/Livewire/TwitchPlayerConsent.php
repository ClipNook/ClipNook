<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

use function view;

final class TwitchPlayerConsent extends Component
{
    public bool $consented = false;

    public bool $showPlayer = false;

    public ?array $clipInfo = null;

    public function mount(?array $clipInfo = null): void
    {
        $this->clipInfo = $clipInfo;
    }

    public function loadPlayer(): void
    {
        $this->showPlayer = true;
        $this->dispatch('twitch-player-loaded');
    }

    public function resetPlayer(): void
    {
        $this->showPlayer = false;
    }

    public function render()
    {
        return view('livewire.twitch-player-consent');
    }
}
