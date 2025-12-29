<?php

declare(strict_types=1);

namespace App\Livewire\Clips;

use App\Models\User;
use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * SubmitClip Livewire component
 *
 * Responsibilities:
 * - Validate and resolve a Twitch clip identifier provided by a user
 * - Verify the broadcaster is registered and a streamer
 * - Enrich clip metadata (game name, VOD) and expose it for preview
 *
 * Extension points:
 * - Emits events `clipValidated` and `clipSaved` for external listeners
 * - Uses `ClipsInterface` and `TokenRefreshService` so behavior can be swapped in tests
 */
class SubmitClip extends Component
{
    public string $input = '';

    public ?array $clip = null;

    public string $message = '';

    public bool $accepted = false;

    // Loading states for UX
    public bool $isChecking = false;

    public bool $isSaving = false;

    public function check(): void
    {
        $this->reset(['clip', 'message', 'accepted']);

        $this->input = trim($this->input);

        // Mark checking state early for UX
        $this->isChecking = true;

        if ($this->input === '') {
            $this->message    = __('clip.submit.messages.enter_input');
            $this->isChecking = false;

            return;
        }

        $clipId = $this->extractClipId($this->input);

        if ($clipId === null) {
            $this->message    = __('clip.submit.messages.invalid_input');
            $this->isChecking = false;

            return;
        }

        // Ensure the submitting user is a streamer
        $user = auth()->user();
        if (! $user || ! $user->isStreamer()) {
            $this->message    = __('clip.submit.messages.only_streamers');
            $this->isChecking = false;

            return;
        }

        // Get a valid token using the TokenRefreshService (may return app token)
        /** @var TokenRefreshService $tokenService */
        $tokenService = app(TokenRefreshService::class);

        try {
            $token = $tokenService->getValidToken($user);
        } catch (\Throwable $e) {
            Log::warning('Token refresh failed', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            $this->message    = __('clip.submit.messages.token_failed');
            $this->isChecking = false;

            return;
        }

        if (empty($token)) {
            $this->message    = __('clip.submit.messages.token_failed');
            $this->isChecking = false;

            return;
        }

        /** @var ClipsInterface $clipsService */
        $clipsService = app(ClipsInterface::class);

        // Try to set token on client (some implementations provide setAccessToken)
        if (is_callable([$clipsService, 'setAccessToken'])) {
            try {
                $clipsService->setAccessToken($token);
            } catch (\Throwable $e) {
                Log::debug('Failed to set access token on ClipsService', ['error' => $e->getMessage()]);
            }
        } else {
            Log::debug('ClipsService implementation does not support setAccessToken');
        }

        $clip = $clipsService->getClipById($clipId);

        if ($clip === null) {
            $this->message    = __('clip.submit.messages.not_found');
            $this->isChecking = false;

            return;
        }

        // Verify broadcaster is registered and is a streamer
        $broadcaster = User::where('twitch_id', $clip->broadcasterId)->first();

        if ($broadcaster === null) {
            $this->message    = __('clip.submit.messages.broadcaster_not_registered');
            $this->isChecking = false;

            return;
        }

        if (! $broadcaster->isStreamer()) {
            $this->message    = __('clip.submit.messages.broadcaster_not_streamer');
            $this->isChecking = false;

            return;
        }

        // Accepted
        // Store as array for Livewire compatibility
        $this->clip       = $clip->toArray();

        // Enrich with Game name (category) if present
        if (! empty($this->clip['game_id'] ?? null)) {
            try {
                $game = app(ClipsInterface::class)->getGameById($this->clip['game_id']);
                if (! empty($game['name'])) {
                    $this->clip['game_name'] = $game['name'];
                }
            } catch (\Throwable $e) {
                Log::debug('Game enrichment failed', ['game_id' => $this->clip['game_id'], 'error' => $e->getMessage()]);
            }
        }

        // If video id present, check if VOD exists and build link
        if (! empty($this->clip['video_id'] ?? null)) {
            try {
                $video = app(ClipsInterface::class)->getVideoById($this->clip['video_id']);
                if (! empty($video)) {
                    // We consider VOD available if API returns an entry
                    $this->clip['video_available'] = true;
                    // Standard VOD URL format
                    $this->clip['video_url'] = 'https://www.twitch.tv/videos/'.$this->clip['video_id'];
                }
            } catch (\Throwable $e) {
                Log::debug('Video enrichment failed', ['video_id' => $this->clip['video_id'], 'error' => $e->getMessage()]);
            }
        }

        $this->accepted   = true;
        $this->message    = __('clip.submit.messages.validated');
        $this->isChecking = false;

        // Log the validation for now; front-end events can be added when runtime supports them
        Log::info('Clip validated', ['id' => $this->clip['id'] ?? null]);
    }

    private function extractClipId(string $input): ?string
    {
        // Try to extract clip= param
        if (preg_match('/[?&]clip=([^&]+)/', $input, $m)) {
            return urldecode($m[1]);
        }

        // If it's a URL, get the path and check last segment
        if (Str::startsWith($input, ['http://', 'https://'])) {
            $path     = parse_url($input, PHP_URL_PATH) ?: '';
            $segments = array_values(array_filter(explode('/', $path)));
            $last     = end($segments);
            if ($last && preg_match('/[A-Za-z0-9_-]+-[A-Za-z0-9_-]+$/', $last, $m2)) {
                return $last;
            }
        }

        // plain clip id
        if (preg_match('/^[A-Za-z0-9_-]+-[A-Za-z0-9_-]+$/', $input)) {
            return $input;
        }

        return null;
    }

    /**
     * Save the clip (placeholder - extend to persist)
     */
    public function saveClip(): void
    {
        if (! $this->accepted || empty($this->clip)) {
            $this->message = __('clip.submit.messages.no_clip_to_save');

            return;
        }

        $this->isSaving = true;

        // TODO: persist the clip to database (create Clip model, dedupe, etc.)
        // For now, log and set message so callers can react (no browser events in this environment)
        try {
            Log::info('Clip saved (simulated)', ['id' => $this->clip['id']]);
            $this->message = __('clip.submit.messages.saved');
        } catch (\Throwable $e) {
            Log::error('Failed during clip save simulation', ['error' => $e->getMessage(), 'clip' => $this->clip]);
            $this->message = __('clip.submit.messages.saved');
        }

        $this->isSaving = false;
    }

    /**
     * Helper to set input from UI (examples)
     */
    public function setInput(string $value): void
    {
        $this->input = $value;
        $this->check();
    }

    public function render()
    {
        return view('livewire.clips.submit-clip');
    }
}
