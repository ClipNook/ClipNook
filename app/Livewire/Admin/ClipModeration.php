<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Clip\ApproveClipAction;
use App\Actions\Clip\DeleteClipAction;
use App\Actions\Clip\RejectClipAction;
use App\Enums\ClipStatus;
use App\Models\Clip;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ClipModeration extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $statusFilter = 'pending';

    public string $searchQuery = '';

    public ?int $selectedClipId = null;

    public string $rejectReason = '';

    public bool $showRejectModal = false;

    /**
     * Escape special characters in search terms for LIKE queries
     */
    protected function escapeSearchTerm(string $term): string
    {
        // Escape % and _ characters that have special meaning in LIKE
        return str_replace(['%', '_'], ['\%', '\_'], $term);
    }

    protected $queryString = [
        'statusFilter' => ['except' => 'pending'],
        'searchQuery'  => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Clip::class);

        if (! auth()->user()->isStaff()) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $clips = Clip::query()
            ->with(['submitter', 'broadcaster', 'game', 'moderator'])
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->searchQuery, function ($query) {
                $escapedQuery = $this->escapeSearchTerm($this->searchQuery);
                $query->where(function ($q) use ($escapedQuery) {
                    $q->where('title', 'like', '%'.$escapedQuery.'%')
                        ->orWhere('twitch_clip_id', 'like', '%'.$escapedQuery.'%')
                        ->orWhereHas('broadcaster', function ($q) use ($escapedQuery) {
                            $q->where('twitch_display_name', 'like', '%'.$escapedQuery.'%');
                        })
                        ->orWhereHas('submitter', function ($q) use ($escapedQuery) {
                            $q->where('twitch_display_name', 'like', '%'.$escapedQuery.'%');
                        });
                });
            })
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending'  => Clip::where('status', ClipStatus::PENDING)->count(),
            'approved' => Clip::where('status', ClipStatus::APPROVED)->count(),
            'rejected' => Clip::where('status', ClipStatus::REJECTED)->count(),
            'flagged'  => Clip::where('status', ClipStatus::FLAGGED)->count(),
        ];

        return view('livewire.admin.clip-moderation', [
            'clips' => $clips,
            'stats' => $stats,
        ]);
    }

    public function approveClip(int $clipId): void
    {
        $clip = Clip::findOrFail($clipId);
        $this->authorize('moderate', $clip);

        app(ApproveClipAction::class)->execute($clip, auth()->user());

        $this->dispatch('clip-approved', clipId: $clipId);
        session()->flash('success', __('clips.clip_approved'));
    }

    public function openRejectModal(int $clipId): void
    {
        $this->selectedClipId  = $clipId;
        $this->showRejectModal = true;
        $this->rejectReason    = '';
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedClipId  = null;
        $this->rejectReason    = '';
    }

    public function rejectClip(): void
    {
        $this->validate([
            'rejectReason' => 'required|string|min:10|max:500',
        ]);

        $clip = Clip::findOrFail($this->selectedClipId);
        $this->authorize('moderate', $clip);

        app(RejectClipAction::class)->execute($clip, auth()->user(), $this->rejectReason);

        $this->closeRejectModal();
        $this->dispatch('clip-rejected', clipId: $this->selectedClipId);
        session()->flash('success', __('clips.clip_rejected'));
    }

    public function deleteClip(int $clipId): void
    {
        $clip = Clip::findOrFail($clipId);
        $this->authorize('delete', $clip);

        app(DeleteClipAction::class)->execute($clip, auth()->user());

        $this->dispatch('clip-deleted', clipId: $clipId);
        session()->flash('success', __('clips.clip_deleted'));
    }

    public function toggleFeatured(int $clipId): void
    {
        $clip = Clip::findOrFail($clipId);
        $this->authorize('moderate', $clip);

        $clip->toggleFeatured();

        $this->dispatch('clip-featured-toggled', clipId: $clipId);
        session()->flash('success', __('clips.clip_featured_toggled'));
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }
}
