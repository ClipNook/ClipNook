<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Livewire\Concerns\WithPagination;
use App\Livewire\Concerns\WithSearch;
use App\Livewire\Concerns\WithSorting;
use Livewire\Attributes\Title;
use App\Models\Clip;
use Livewire\Component;
use Livewire\WithPagination as LivewirePagination;

use function auth;
use function view;

#[Title('Clips Library')] 
final class ClipList extends Component
{
    use LivewirePagination;
    use WithPagination;
    use WithSearch;
    use WithSorting;

    protected array $sortableColumns = ['created_at', 'upvotes', 'view_count', 'duration'];

    public function mount(): void
    {
        $this->perPage = auth()->user()?->appearance_settings['clips_per_page'] ?? 12;
    }

    public function render(): \Illuminate\View\View
    {
        $clips = Clip::withRelations()
            ->approved()
            ->when($this->search, fn ($query) => $query->search($this->search))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.clips.clip-list', [
            'clips' => $clips,
        ]);
    }
}
