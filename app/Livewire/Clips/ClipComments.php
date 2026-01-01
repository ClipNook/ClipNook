<?php

namespace App\Livewire\Clips;

use App\Models\Clip;
use App\Models\ClipComment;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClipComments extends Component
{
    public Clip $clip;

    #[Validate('required|min:1|max:1000')]
    public string $newComment = '';

    public ?int $replyToId = null;

    public function postComment(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('auth.login'));

            return;
        }

        $this->validate();

        ClipComment::create([
            'clip_id'   => $this->clip->id,
            'user_id'   => auth()->id(),
            'content'   => $this->newComment,
            'parent_id' => $this->replyToId,
        ]);

        $this->newComment = '';
        $this->replyToId  = null;

        session()->flash('message', __('clips.comment_posted'));
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ClipComment::query()->findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        session()->flash('message', __('clips.comment_deleted_success'));
    }

    public function setReplyTo(int $commentId): void
    {
        $this->replyToId = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
    }

    public function render()
    {
        $comments = ClipComment::query()
            ->where('clip_id', $this->clip->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.clips.clip-comments', [
            'comments' => $comments,
        ]);
    }
}
