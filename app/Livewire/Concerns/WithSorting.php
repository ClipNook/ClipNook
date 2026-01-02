<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use function in_array;

/**
 * Handles sorting functionality for Livewire components.
 */
trait WithSorting
{
    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function sortBy(string $column): void
    {
        if (! in_array($column, $this->sortableColumns, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy        = $column;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    public function isSortedBy(string $column): bool
    {
        return $this->sortBy === $column;
    }

    public function getSortDirectionFor(string $column): string
    {
        return $this->isSortedBy($column) ? $this->sortDirection : 'none';
    }

    public function getSortIcon(string $column): string
    {
        if (! $this->isSortedBy($column)) {
            return 'fa-sort';
        }

        return $this->sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    }
}
