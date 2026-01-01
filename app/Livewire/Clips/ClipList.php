<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Models\Clip;
use Livewire\Component;
use Livewire\WithPagination;

class ClipList extends Component
{
    use WithPagination;

    public $perPage = 12;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $clips = Clip::with(['submitter', 'broadcaster', 'game'])
            ->approved()
            ->when($this->search, fn ($query) => $query->search($this->search))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.clips.clip-list', [
            'clips' => $clips,
        ]);
    }
}
