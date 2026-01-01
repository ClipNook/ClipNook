<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

/**
 * Handles pagination functionality for Livewire components.
 */
trait WithPagination
{
    public int $perPage = 12;

    public array $perPageOptions = [12, 24, 48, 96];

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function getPerPageOptionsProperty(): array
    {
        return $this->perPageOptions;
    }
}
