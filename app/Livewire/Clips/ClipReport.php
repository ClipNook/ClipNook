<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipReport as ClipReportModel;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

use function __;
use function auth;
use function route;
use function session;

final class ClipReport extends Component
{
    public Clip $clip;

    public ?ClipComment $comment = null;

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

        $this->authorize('create', ClipReportModel::class);

        // Check if user already reported this clip or comment
        $query = ClipReportModel::query()
            ->where('user_id', auth()->id());

        if ($this->comment) {
            $query->where('comment_id', $this->comment->id);
        } else {
            $query->where('clip_id', $this->clip->id);
        }

        $existingReport = $query->exists();

        if ($existingReport) {
            $this->dispatch('notify', type: 'error', message: __('clips.report_already_submitted'));

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

        // Rate limiting: 5 reports per hour
        $key = 'report:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->dispatch('notify', type: 'error', message: __('clips.too_many_reports'));
            $this->closeModal();

            return;
        }

        RateLimiter::hit($key, 3600);

        $this->validate();

        $data = [
            'user_id'     => auth()->id(),
            'reason'      => ReportReason::from($this->reason),
            'status'      => ReportStatus::PENDING,
            'description' => $this->description,
        ];

        if ($this->comment) {
            $data['comment_id'] = $this->comment->id;
            $data['clip_id'] = $this->comment->clip_id;
        } else {
            $data['clip_id'] = $this->clip->id;
        }

        ClipReportModel::create($data);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: __('clips.report_success'));
    }

    #[Computed]
    public function reportType(): string
    {
        return $this->comment ? 'comment' : 'clip';
    }

    #[Computed]
    public function reportText(): string
    {
        return $this->comment ? __('clips.report_comment') : __('clips.report_clip');
    }

    #[Computed]
    public function reportTitle(): string
    {
        return $this->comment ? __('clips.report_comment_title') : __('clips.report_title');
    }
}
