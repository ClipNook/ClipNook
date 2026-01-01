<?php

declare(strict_types=1);

namespace App\Actions\Clip;

use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Jobs\ProcessClipSubmission;
use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\Api\ClipApiService;
use App\Services\Twitch\Api\GameApiService;
use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Api\VideoApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SubmitClipAction
{
    public function __construct(
        private ClipApiService $clipApiService,
        private GameApiService $gameApiService,
        private StreamerApiService $streamerApiService,
        private VideoApiService $videoApiService,
        private TwitchTokenManager $tokenManager
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
        $this->ensureUserHasTwitchTokens($user);

        $cacheKey = "processing_clip_{$twitchClipId}";

        if (Cache::has($cacheKey)) {
            throw ValidationException::withMessages(['twitch_clip_id' => [__('clips.clip_processing')]]);
        }

        $clipData = $this->validateClip($twitchClipId, $user);
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
        $this->ensureUserHasTwitchTokens($user);

        return DB::transaction(function () use ($user, $twitchClipId) {
            $clipData = $this->validateClip($twitchClipId, $user);
            $this->checkExistingClip($twitchClipId);
            $broadcaster = $this->validateBroadcaster($clipData->broadcasterId);
            $this->checkPermissions($user, $broadcaster);

            $game = $clipData->gameId
                ? $this->getOrCreateGame($clipData->gameId, $this->tokenManager->getValidAccessToken($user))
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

            // Download thumbnail synchronously
            if ($clipData->thumbnailUrl) {
                $this->downloadThumbnail($clip, $clipData->thumbnailUrl);
            }

            // Download game box art synchronously if game exists
            if ($game && $game->box_art_url) {
                $this->downloadGameBoxArt($game, $game->box_art_url);
            }

            return $clip;
        });
    }

    private function validateClip(string $twitchClipId, User $user): \App\Services\Twitch\DTOs\ClipDTO
    {
        $accessToken = $this->tokenManager->getValidAccessToken($user);
        $clipData    = $this->clipApiService->getClip($twitchClipId, $accessToken);

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

    private function downloadThumbnail(Clip $clip, string $thumbnailUrl): void
    {
        try {
            $thumbnailPath = 'clips/thumbnails/'.$clip->id.'.jpg';

            // Ensure directory exists
            Storage::disk('public')->makeDirectory(dirname($thumbnailPath));

            // Download the image
            $imageContent = file_get_contents($thumbnailUrl);
            if ($imageContent !== false) {
                Storage::disk('public')->put($thumbnailPath, $imageContent);
                $clip->update(['local_thumbnail_path' => $thumbnailPath]);
                Log::info('Thumbnail downloaded successfully', ['clip_id' => $clip->id, 'path' => $thumbnailPath]);
            } else {
                Log::warning('Failed to download thumbnail', ['clip_id' => $clip->id, 'url' => $thumbnailUrl]);
            }
        } catch (\Exception $e) {
            Log::error('Thumbnail download failed', ['clip_id' => $clip->id, 'error' => $e->getMessage()]);
        }
    }

    private function downloadGameBoxArt(\App\Models\Game $game, string $boxArtUrl): void
    {
        try {
            $resolvedUrl = str_replace(
                ['{width}', '{height}'],
                ['285', '380'],
                $boxArtUrl
            );
            $boxArtPath = 'games/box-art/'.$game->id.'.jpg';

            // Ensure directory exists
            Storage::disk('public')->makeDirectory(dirname($boxArtPath));

            // Download the image
            $imageContent = file_get_contents($resolvedUrl);
            if ($imageContent !== false) {
                Storage::disk('public')->put($boxArtPath, $imageContent);
                $game->update(['local_box_art_path' => $boxArtPath]);
                Log::info('Game box art downloaded successfully', ['game_id' => $game->id, 'path' => $boxArtPath]);
            } else {
                Log::warning('Failed to download game box art', ['game_id' => $game->id, 'url' => $resolvedUrl]);
            }
        } catch (\Exception $e) {
            Log::error('Game box art download failed', ['game_id' => $game->id, 'error' => $e->getMessage()]);
        }
    }

    private function extractTags(\App\Services\Twitch\DTOs\ClipDTO $clipData): array
    {
        return array_unique(array_filter([
            $clipData->broadcasterName,
            $clipData->language,
        ]));
    }

    private function ensureUserHasTwitchTokens(User $user): void
    {
        if (! $user->twitch_access_token) {
            throw ValidationException::withMessages(['user' => [__('twitch.missing_access_token')]]);
        }
    }

    private function getOrCreateGame(string $gameId, ?string $accessToken = null): ?\App\Models\Game
    {
        $gameDTO = $this->gameApiService->getGame($gameId, $accessToken);

        if (! $gameDTO) {
            return null;
        }

        return \App\Models\Game::findOrCreateFromTwitch([
            'id'          => $gameDTO->id,
            'name'        => $gameDTO->name,
            'box_art_url' => $gameDTO->boxArtUrl,
            'igdb_id'     => null,
        ]);
    }
}
