<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

use function array_key_exists;
use function session;
use function view;

final class ThemeSelector extends Component
{
    public string $currentTheme = 'violet';

    public bool $isOpen = false;

    public array $availableThemes = [
        'violet' => [
            'name'        => 'Violet',
            'primary'     => 'violet-600',
            'secondary'   => 'violet-500',
            'bg'          => 'bg-violet-600',
            'bgSecondary' => 'bg-violet-500',
        ],
        'blue' => [
            'name'        => 'Blue',
            'primary'     => 'blue-600',
            'secondary'   => 'blue-500',
            'bg'          => 'bg-blue-600',
            'bgSecondary' => 'bg-blue-500',
        ],
        'green' => [
            'name'        => 'Green',
            'primary'     => 'emerald-600',
            'secondary'   => 'emerald-500',
            'bg'          => 'bg-emerald-600',
            'bgSecondary' => 'bg-emerald-500',
        ],
        'red' => [
            'name'        => 'Red',
            'primary'     => 'red-600',
            'secondary'   => 'red-500',
            'bg'          => 'bg-red-600',
            'bgSecondary' => 'bg-red-500',
        ],
        'orange' => [
            'name'        => 'Orange',
            'primary'     => 'orange-600',
            'secondary'   => 'orange-500',
            'bg'          => 'bg-orange-600',
            'bgSecondary' => 'bg-orange-500',
        ],
        'pink' => [
            'name'        => 'Pink',
            'primary'     => 'pink-600',
            'secondary'   => 'pink-500',
            'bg'          => 'bg-pink-600',
            'bgSecondary' => 'bg-pink-500',
        ],
        'cyan' => [
            'name'        => 'Cyan',
            'primary'     => 'cyan-600',
            'secondary'   => 'cyan-500',
            'bg'          => 'bg-cyan-600',
            'bgSecondary' => 'bg-cyan-500',
        ],
        'amber' => [
            'name'        => 'Amber',
            'primary'     => 'amber-600',
            'secondary'   => 'amber-500',
            'bg'          => 'bg-amber-600',
            'bgSecondary' => 'bg-amber-500',
        ],
    ];

    public function mount(): void
    {
        $this->currentTheme = session('theme', 'violet');
    }

    public function setTheme(string $theme): void
    {
        if (! array_key_exists($theme, $this->availableThemes)) {
            return;
        }

        $this->currentTheme = $theme;
        session(['theme' => $theme]);

        $this->dispatch('theme-changed', theme: $theme);
    }

    public function toggleSelector(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function render()
    {
        return view('livewire.theme-selector');
    }
}
