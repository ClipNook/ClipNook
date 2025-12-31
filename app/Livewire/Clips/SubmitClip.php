<?php

namespace App\Livewire\Clips;

use App\Actions\Clip\SubmitClipAction;
use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Services\Twitch\TwitchService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SubmitClip extends Component
{
    #[Validate('required|string|regex:/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]{1,100})$/')]
    public string $twitchClipId = '';

    public bool $isSubmitting = false;

    public bool $isChecking = false;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public ?array $clipInfo = null;

    public bool $showPlayer = false;

    protected $listeners = [
        'clip-submitted' => 'handleClipSubmitted',
        'twitch-player-loaded' => 'handlePlayerLoaded',
    ];

    public function checkClip()
    {
        $this->resetMessages();
        $this->isChecking = true;

        try {
            $this->validate();

            $clipId   = $this->extractClipId($this->twitchClipId);
            $clipData = app(TwitchService::class)->getClip($clipId);

            if (! $clipData) {
                $this->errorMessage = __('clips.clip_not_found');

                return;
            }

            $this->clipInfo = [
                'id'              => $clipData->id,
                'title'           => $clipData->title,
                'broadcasterName' => $clipData->broadcasterName,
                'creatorName'     => $clipData->creatorName,
                'viewCount'       => $clipData->viewCount,
                'createdAt'       => $clipData->createdAt,
                'duration'        => $clipData->duration,
                'thumbnailUrl'    => $clipData->thumbnailUrl,
                'embedUrl'        => $clipData->embedUrl,
            ];

        } catch (ValidationException $e) {
            $errors             = $e->errors();
            $this->errorMessage = $errors['twitch_clip_id'][0] ?? $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = __('clips.unexpected_error');
            report($e);
        } finally {
            $this->isChecking = false;
        }
    }

    public function loadPlayer()
    {
        $this->showPlayer = true;
    }

    public function resetClip()
    {
        $this->clipInfo    = null;
        $this->showPlayer  = false;
        $this->twitchClipId = '';
        $this->resetMessages();
    }

    public function submit()
    {
        if (! $this->clipInfo) {
            $this->errorMessage = __('clips.unexpected_error');

            return;
        }

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
            $clipId = $this->clipInfo['id'];
            app(SubmitClipAction::class)->execute(auth()->user(), $clipId);

            RateLimiter::hit($key);

            $this->resetClip();
            $this->successMessage = __('clips.submission_success');
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

    public function handlePlayerLoaded()
    {
        $this->showPlayer = true;
    }

    public function handleClipSubmitted()
    {
        $this->dispatch('refresh-clip-list');
    }

    private function resetMessages()
    {
        $this->successMessage = null;
        $this->errorMessage   = null;
    }

    private function extractClipId(string $input): string
    {
        if (preg_match('/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]+)$/', $input, $matches)) {
            return $matches[1];
        }

        return $input;
    }

    public function render()
    {
        return view('livewire.clips.submit-clip');
    }
}
