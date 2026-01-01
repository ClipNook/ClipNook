<?php

namespace App\Livewire\Clips;

use App\Models\Clip;
use App\Models\ClipVote;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClipRating extends Component
{
    public Clip $clip;

    public ?string $userVote = null;

    public int $upvotes = 0;

    public int $downvotes = 0;

    public function mount(): void
    {
        $this->upvotes   = $this->clip->upvotes;
        $this->downvotes = $this->clip->downvotes;

        if (auth()->check()) {
            $existingVote = ClipVote::query()
                ->where('clip_id', $this->clip->id)
                ->where('user_id', auth()->id())
                ->first();

            $this->userVote = $existingVote?->vote_type;
        }
    }

    public function vote(string $type): void
    {
        if (! auth()->check()) {
            $this->redirect(route('auth.login'));

            return;
        }

        DB::transaction(function () use ($type) {
            $existingVote = ClipVote::query()
                ->where('clip_id', $this->clip->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingVote) {
                if ($existingVote->vote_type === $type) {
                    // Remove vote
                    $existingVote->delete();
                    $this->decrementVote($type);
                    $this->userVote = null;
                    session()->flash('message', __('clips.vote_removed'));
                } else {
                    // Change vote
                    $this->decrementVote($existingVote->vote_type);
                    $existingVote->update(['vote_type' => $type]);
                    $this->incrementVote($type);
                    $this->userVote = $type;
                    session()->flash('message', __('clips.vote_success'));
                }
            } else {
                // New vote
                ClipVote::create([
                    'clip_id'   => $this->clip->id,
                    'user_id'   => auth()->id(),
                    'vote_type' => $type,
                ]);
                $this->incrementVote($type);
                $this->userVote = $type;
                session()->flash('message', __('clips.vote_success'));
            }
        });
    }

    protected function incrementVote(string $type): void
    {
        if ($type === 'upvote') {
            $this->clip->increment('upvotes');
            $this->upvotes++;
        } else {
            $this->clip->increment('downvotes');
            $this->downvotes++;
        }
    }

    protected function decrementVote(string $type): void
    {
        if ($type === 'upvote') {
            $this->clip->decrement('upvotes');
            $this->upvotes--;
        } else {
            $this->clip->decrement('downvotes');
            $this->downvotes--;
        }
    }

    public function render()
    {
        return view('livewire.clips.clip-rating');
    }
}
