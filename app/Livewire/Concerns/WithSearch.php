<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

/**
 * Handles search functionality for Livewire components.
 */
trait WithSearch
{
    public string $search = '';

    protected array $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function hasSearchQuery(): bool
    {
        return ! empty(trim($this->search));
    }
}
