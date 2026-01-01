<?php

namespace App\Livewire\Clips;

use App\Enums\VoteType;
use App\Models\Clip;
use App\Models\ClipVote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ClipRating extends Component
{
    public Clip $clip;

    public ?VoteType $userVote = null;

    public int $upvotes = 0;

    public int $downvotes = 0;

    public function mount(): void
    {
        $this->upvotes   = $this->clip->upvotes;
        $this->downvotes = $this->clip->downvotes;

        if ($user = auth()->user()) {
            $this->userVote = ClipVote::query()
                ->where('clip_id', $this->clip->id)
                ->where('user_id', $user->id)
                ->value('vote_type');
        }
    }

    public function vote(string $type): void
    {
        if (! auth()->check()) {
            $this->redirect(route('auth.login'));

            return;
        }

        $voteType = VoteType::from($type);

        // Rate limiting: 10 votes per minute
        $key = 'vote:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, config('constants.rate_limiting.vote_max_attempts'))) {
            session()->flash('error', __('clips.too_many_votes'));

            return;
        }

        RateLimiter::hit($key, config('constants.rate_limiting.vote_decay_minutes') * 60);

        DB::transaction(function () use ($voteType) {
            $existingVote = ClipVote::query()
                ->where('clip_id', $this->clip->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingVote) {
                if ($existingVote->vote_type === $voteType) {
                    // Remove vote
                    $existingVote->delete();
                    $this->decrementVote($voteType);
                    $this->userVote = null;
                    session()->flash('message', __('clips.vote_removed'));
                } else {
                    // Change vote
                    $this->decrementVote($existingVote->vote_type);
                    $existingVote->update(['vote_type' => $voteType]);
                    $this->incrementVote($voteType);
                    $this->userVote = $voteType;
                    session()->flash('message', __('clips.vote_success'));
                }
            } else {
                // New vote
                ClipVote::create([
                    'clip_id'   => $this->clip->id,
                    'user_id'   => auth()->id(),
                    'vote_type' => $voteType,
                ]);
                $this->incrementVote($voteType);
                $this->userVote = $voteType;
                session()->flash('message', __('clips.vote_success'));
            }
        });
    }

    protected function incrementVote(VoteType $type): void
    {
        if ($type === VoteType::UPVOTE) {
            $this->clip->increment('upvotes');
            $this->upvotes++;
        } else {
            $this->clip->increment('downvotes');
            $this->downvotes++;
        }
    }

    protected function decrementVote(VoteType $type): void
    {
        if ($type === VoteType::UPVOTE) {
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
