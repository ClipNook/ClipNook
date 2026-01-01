<?php

declare(strict_types=1);

namespace App\Actions\Clip;

use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Jobs\ProcessClipSubmission;
use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\TwitchGameService;
use App\Services\Twitch\TwitchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubmitClipAction
{
    public function __construct(
        private TwitchService $twitchService,
        private TwitchGameService $twitchGameService
    ) {}

    /**
     * Submit a clip from Twitch
     *
     * This action handles the initial clip submission validation and dispatches
     * a background job for processing. This improves response times and handles
     * API rate limits gracefully.
     *
     * @param  User  $user  The user submitting the clip
     * @param  string  $twitchClipId  The Twitch clip ID
     * @return bool True if job was dispatched successfully
     *
     * @throws ClipNotFoundException When clip is not found on Twitch
     * @throws BroadcasterNotRegisteredException When broadcaster is not registered
     * @throws ClipPermissionException When user lacks permission
     */
    public function execute(User $user, string $twitchClipId): bool
    {
        $cacheKey = "processing_clip_{$twitchClipId}";

        if (Cache::has($cacheKey)) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('clips.clip_processing')]]);
        }

        $clipData = $this->validateClip($twitchClipId);
        $this->checkExistingClip($twitchClipId);
        $broadcaster = $this->validateBroadcaster($clipData->broadcasterId);
        $this->checkPermissions($user, $broadcaster);

        Cache::put($cacheKey, true, now()->addMinutes(10));

        ProcessClipSubmission::dispatch($user, $twitchClipId);

        Log::info('Clip submission job dispatched', [
            'user_id'        => $user->id,
            'twitch_clip_id' => $twitchClipId,
            'broadcaster_id' => $broadcaster->id,
        ]);

        return true;
    }

    public function executeSync(User $user, string $twitchClipId): Clip
    {
        return DB::transaction(function () use ($user, $twitchClipId) {
            $clipData = $this->validateClip($twitchClipId);
            $this->checkExistingClip($twitchClipId);
            $broadcaster = $this->validateBroadcaster($clipData->broadcasterId);
            $this->checkPermissions($user, $broadcaster);

            $game = $clipData->gameId
                ? $this->twitchGameService->getOrCreateGame($clipData->gameId)
                : null;

            $clip = Clip::create([
                'submitter_id'      => $user->id,
                'submitted_at'      => now(),
                'twitch_clip_id'    => $twitchClipId,
                'title'             => $clipData->title,
                'description'       => null,
                'url'               => $clipData->url,
                'thumbnail_url'     => $clipData->thumbnailUrl,
                'duration'          => $clipData->duration,
                'view_count'        => $clipData->viewCount,
                'created_at_twitch' => $clipData->createdAt,
                'clip_creator_name' => $clipData->creatorName,
                'broadcaster_id'    => $broadcaster->id,
                'game_id'           => $game?->id,
                'tags'              => $this->extractTags($clipData),
            ]);

            \App\Events\ClipSubmitted::dispatch($clip, $user);

            return $clip;
        });
    }

    private function validateClip(string $twitchClipId): \App\Services\Twitch\DTOs\ClipDTO
    {
        $clipData = $this->twitchService->getClip($twitchClipId);

        if (! $clipData) {
            throw ClipNotFoundException::forId($twitchClipId);
        }

        $this->validateClipRules($clipData);

        return $clipData;
    }

    private function checkExistingClip(string $twitchClipId): void
    {
        if (Clip::where('twitch_clip_id', $twitchClipId)->exists()) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('clips.clip_already_submitted')]]);
        }
    }

    private function validateBroadcaster(string $broadcasterId): User
    {
        $broadcaster = User::where('twitch_id', $broadcasterId)->first();

        if (! $broadcaster) {
            throw BroadcasterNotRegisteredException::forTwitchId($broadcasterId);
        }

        return $broadcaster;
    }

    private function checkPermissions(User $user, User $broadcaster): void
    {
        if (! $user->canSubmitClipsFor($broadcaster)) {
            throw ClipPermissionException::cannotSubmitForBroadcaster($broadcaster->id);
        }
    }

    private function validateClipRules(\App\Services\Twitch\DTOs\ClipDTO $clipData): void
    {
        $rules = config('twitch.validation_rules', [
            'max_clip_age_days' => 7,
            'max_view_count'    => 100000,
            'max_duration'      => 60,
        ]);

        $clipAge = now()->diffInDays($clipData->createdAt);
        if ($clipAge > $rules['max_clip_age_days']) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('twitch.clip_too_old', ['days' => $rules['max_clip_age_days']])]]);
        }

        if ($clipData->viewCount > $rules['max_view_count']) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('twitch.too_many_views')]]);
        }

        if ($clipData->duration > $rules['max_duration']) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('twitch.clip_too_long', ['seconds' => $rules['max_duration']])]]);
        }
    }

    private function extractTags(\App\Services\Twitch\DTOs\ClipDTO $clipData): array
    {
        return array_unique(array_filter([
            $clipData->broadcasterName,
            $clipData->language,
        ]));
    }
}
