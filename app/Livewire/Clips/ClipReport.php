<?php

namespace App\Livewire\Clips;

use App\Models\Clip;
use App\Models\ClipReport as ClipReportModel;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClipReport extends Component
{
    public Clip $clip;

    public bool $showModal = false;

    #[Validate('required|in:inappropriate,spam,copyright,misleading,other')]
    public string $reason = 'inappropriate';

    #[Validate('nullable|max:500')]
    public string $description = '';

    public function openModal(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('auth.login'));

            return;
        }

        // Check if user already reported this clip
        $existingReport = ClipReportModel::query()
            ->where('clip_id', $this->clip->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($existingReport) {
            session()->flash('error', __('clips.report_already_submitted'));

            return;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['reason', 'description']);
    }

    public function submitReport(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('auth.login'));

            return;
        }

        $this->validate();

        ClipReportModel::create([
            'clip_id'     => $this->clip->id,
            'user_id'     => auth()->id(),
            'reason'      => $this->reason,
            'description' => $this->description,
        ]);

        $this->closeModal();
        session()->flash('message', __('clips.report_success'));
    }

    public function render()
    {
        return view('livewire.clips.clip-report');
    }
}
