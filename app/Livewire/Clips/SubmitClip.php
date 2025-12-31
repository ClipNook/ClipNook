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
    #[Validate('required|string|regex:/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]+)$/')]
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
            $this->errorMessage = "Too many submissions. Try again in {$seconds} seconds.";

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

            $this->successMessage = 'Clip submitted successfully! It will be processed in the background.';
            $this->twitchClipId   = '';
            $this->dispatch('clip-submitted');

        } catch (ClipNotFoundException $e) {
            $this->errorMessage = 'This clip was not found on Twitch. Please check the ID and try again.';
        } catch (BroadcasterNotRegisteredException $e) {
            $this->errorMessage = 'The broadcaster of this clip is not registered with our service.';
        } catch (ClipPermissionException $e) {
            $this->errorMessage = 'You do not have permission to submit clips for this broadcaster.';
        } catch (ValidationException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'An unexpected error occurred. Please try again later.';
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
