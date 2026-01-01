<?php

namespace App\Livewire\Games;

use App\Models\Game;
use Livewire\Component;
use Livewire\WithPagination;

class GameList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'clips';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'clips'],
    ];

    /**
     * Escape special characters in search terms for LIKE queries
     */
    protected function escapeSearchTerm(string $term): string
    {
        // Escape % and _ characters that have special meaning in LIKE
        return str_replace(['%', '_'], ['\%', '\_'], $term);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $games = Game::query()
            ->when($this->search, function ($query) {
                $escapedSearch = $this->escapeSearchTerm($this->search);
                $query->where('name', 'like', '%'.$escapedSearch.'%');
            })
            ->withCount(['clips' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->when($this->sortBy === 'clips', fn ($q) => $q->orderBy('clips_count', 'desc'))
            ->when($this->sortBy === 'alphabetical', fn ($q) => $q->orderBy('name'))
            ->when($this->sortBy === 'recent', fn ($q) => $q->latest())
            ->paginate(24);

        return view('livewire.games.game-list', [
            'games' => $games,
        ]);
    }
}
