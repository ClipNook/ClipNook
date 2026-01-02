<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Actions\Clip\SubmitClipAction;
use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Services\Twitch\Api\ClipApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Exception;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

use function __;
use function app;
use function auth;
use function config;
use function data_get;
use function preg_match;
use function report;
use function view;

final class SubmitClip extends Component
{
    private const TWITCH_CLIP_URL_REGEX = '/^(?:https?:\/\/(?:www\.)?twitch\.tv\/[^\/]+\/clip\/)?([a-zA-Z0-9_-]{1,100})$/';

    #[Validate('required|string|regex:'.self::TWITCH_CLIP_URL_REGEX)]
    public string $twitchClipId = '';

    public bool $isSubmitting = false;

    public bool $isChecking = false;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public ?array $clipInfo = null;

    public bool $showPlayer = false;

    protected ?ClipApiService $clipApiService = null;

    protected ?TwitchTokenManager $tokenManager = null;

    protected $listeners = [
        'clip-submitted'       => 'handleClipSubmitted',
        'twitch-player-loaded' => 'handlePlayerLoaded',
    ];

    public function mount(ClipApiService $clipApiService, TwitchTokenManager $tokenManager): void
    {
        $this->clipApiService = $clipApiService;
        $this->tokenManager   = $tokenManager;
    }

    public function checkClip(): void
    {
        $this->resetMessages();
        $this->isChecking = true;

        try {
            $this->validate();

            $clipId      = $this->extractClipId($this->twitchClipId);
            $service     = $this->clipApiService ??= app(ClipApiService::class);
            $accessToken = $this->tokenManager ? $this->tokenManager->getValidAccessToken(auth()->user()) : null;
            $clipData    = $service->getClip($clipId, $accessToken);

            if (! $clipData || ! $clipData->id) {
                $this->errorMessage = __('clips.clip_not_found');
                $this->clipInfo     = null;

                return;
            }

            $this->clipInfo = [
                'id'              => $clipData->id,
                'twitchClipId'    => $clipId, // Original Twitch Clip ID
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
        } catch (Exception $e) {
            $this->errorMessage = __('clips.unexpected_error');
            report($e);
        } finally {
            $this->isChecking = false;
        }
    }

    public function loadPlayer(): void
    {
        $this->showPlayer = true;
    }

    public function resetClip(): void
    {
        $this->clipInfo     = null;
        $this->showPlayer   = false;
        $this->twitchClipId = '';
        $this->resetMessages();
    }

    public function submit(): void
    {
        if (empty($this->clipInfo) || ! isset($this->clipInfo['id'])) {
            $this->errorMessage = __('clips.please_check_clip_first');

            return;
        }

        // Rate limiting
        $key = 'submit-clip:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, config('constants.rate_limiting.submit_clip_max_attempts'))) {
            $seconds            = RateLimiter::availableIn($key);
            $this->errorMessage = __('clips.rate_limit_exceeded', ['seconds' => $seconds]);

            return;
        }

        $this->isSubmitting = true;

        try {
            $clipId = data_get($this->clipInfo, 'id');

            if (! $clipId) {
                throw ValidationException::withMessages(['twitch_clip_id' => [__('clips.please_check_clip_first')]]);
            }

            app(SubmitClipAction::class)->executeSync(auth()->user(), $clipId);

            RateLimiter::hit($key, config('constants.rate_limiting.submit_clip_decay_minutes') * 60);

            $this->resetClip();
            $this->resetMessages();
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
        } catch (Exception $e) {
            $this->errorMessage = __('clips.unexpected_error');
            report($e);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function handlePlayerLoaded(): void
    {
        $this->showPlayer = true;
    }

    public function handleClipSubmitted(): void
    {
        $this->dispatch('refresh-clip-list');
    }

    public function render()
    {
        return view('livewire.clips.submit-clip');
    }

    private function resetMessages(): void
    {
        $this->successMessage = null;
        $this->errorMessage   = null;
        $this->clipInfo       = null;
    }

    private function extractClipId(string $input): string
    {
        if (preg_match(self::TWITCH_CLIP_URL_REGEX, $input, $matches)) {
            return $matches[1];
        }

        return $input;
    }
}
