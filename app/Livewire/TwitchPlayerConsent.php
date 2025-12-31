<?php

namespace App\Livewire;

use Livewire\Component;

class TwitchPlayerConsent extends Component
{
    public bool $consented = false;
    public bool $showPlayer = false;
    public ?array $clipInfo = null;

    public function mount(?array $clipInfo = null)
    {
        $this->clipInfo = $clipInfo;
    }

    public function loadPlayer()
    {
        $this->showPlayer = true;
        $this->dispatch('twitch-player-loaded');
    }

    public function resetPlayer()
    {
        $this->showPlayer = false;
    }

    public function render()
    {
        return view('livewire.twitch-player-consent');
    }
}
