<?php

declare(strict_types=1);

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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $games = Game::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
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
