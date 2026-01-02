<?php

declare(strict_types=1);

namespace App\Livewire\Games;

use App\Models\Game;
use Livewire\Component;
use Livewire\WithPagination;

use function __;
use function view;

final class GameList extends Component
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

    public function render(): \Illuminate\View\View
    {
        $games = Game::query()
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->withCount(['clips' => static function ($query): void {
                $query->where('status', 'approved');
            }])
            ->when($this->sortBy === 'clips', static fn ($q) => $q->orderBy('clips_count', 'desc'))
            ->when($this->sortBy === 'alphabetical', static fn ($q) => $q->orderBy('name'))
            ->when($this->sortBy === 'recent', static fn ($q) => $q->latest())
            ->paginate(24);

        return view('livewire.games.game-list', [
            'games' => $games,
        ])->title(__('games.games_page_title'));
    }
}
