<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Models\Clip;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for displaying a paginated overview of all clips.
 *
 * Shows a paginated list of all clips with their related broadcaster and category.
 */
class ClipOverview extends Component
{
    use WithPagination;

    /**
     * Number of clips per page.
     *
     * @var int
     */
    public int $perPage = 12;

    /**
     * Render the overview with pagination and eager-loaded relationships.
     *
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\View\View
    {
        $clips = Clip::with(['broadcaster', 'category'])
            ->orderByDesc('clip_created_at')
            ->paginate($this->perPage);

        return view('livewire.clips.clip-overview', [
            'clips' => $clips,
        ]);
    }
}
