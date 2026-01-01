<?php

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

    /**
     * Escape special characters in search terms for LIKE queries
     */
    protected function escapeSearchTerm(string $term): string
    {
        // Escape % and _ characters that have special meaning in LIKE
        return str_replace(['%', '_'], ['\%', '\_'], $term);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clips = Clip::with(['submitter', 'broadcaster'])
            ->approved()
            ->when($this->search, function ($query) {
                $escapedSearch = $this->escapeSearchTerm($this->search);
                $query->where('title', 'like', '%'.$escapedSearch.'%')
                    ->orWhere('twitch_clip_id', 'like', '%'.$escapedSearch.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.clips.clip-list', [
            'clips' => $clips,
        ]);
    }
}
