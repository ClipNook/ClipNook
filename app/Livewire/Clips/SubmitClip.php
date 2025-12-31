<?php

namespace App\Livewire\Clips;

use App\Actions\Clip\SubmitClipAction;
use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SubmitClip extends Component
{
    #[Validate('required|string|regex:/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]{1,100})$/')]
    public string $twitchClipId = '';

    public bool $isSubmitting = false;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    protected $listeners = [
        'clip-submitted' => 'handleClipSubmitted',
    ];

    public function submit()
    {
        // Rate limiting
        $rateLimit = config('clip.rate_limiting.submit_clip');
        $key       = 'submit-clip:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, $rateLimit['max_attempts'])) {
            $seconds            = RateLimiter::availableIn($key);
            $this->errorMessage = __('clips.rate_limit_exceeded', ['seconds' => $seconds]);

            return;
        }

        $this->resetMessages();
        $this->isSubmitting = true;

        try {
            $this->validate();

            // Extract clip ID from URL if needed
            $clipId = $this->extractClipId($this->twitchClipId);

            // Execute the submission
            app(SubmitClipAction::class)->execute(auth()->user(), $clipId);

            RateLimiter::hit($key);

            $this->successMessage = __('clips.submission_success');
            $this->twitchClipId   = '';
            $this->dispatch('clip-submitted');

        } catch (ClipNotFoundException $e) {
            $this->errorMessage = __('clips.clip_not_found');
        } catch (BroadcasterNotRegisteredException $e) {
            $this->errorMessage = __('clips.broadcaster_not_registered');
        } catch (ClipPermissionException $e) {
            $this->errorMessage = __('clips.permission_denied');
        } catch (ValidationException $e) {
            $errors             = $e->errors();
            $this->errorMessage = $errors['twitch_clip_id'][0] ?? $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = __('clips.unexpected_error');
            report($e);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function handleClipSubmitted()
    {
        // Could trigger UI updates, refresh lists, etc.
        $this->dispatch('refresh-clip-list');
    }

    private function resetMessages()
    {
        $this->successMessage = null;
        $this->errorMessage   = null;
    }

    private function extractClipId(string $input): string
    {
        // If it's a full URL, extract the clip ID
        if (preg_match('/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]+)$/', $input, $matches)) {
            return $matches[1];
        }

        // Fallback: return as-is (shouldn't happen due to validation)
        return $input;
    }

    public function render()
    {
        return view('livewire.clips.submit-clip');
    }
}
