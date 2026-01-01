<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\Api\ClipApiService;
use App\Services\Twitch\Api\GameApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Job for processing clip submissions asynchronously.
 *
 * This job handles the complete clip submission workflow in the background,
 * including Twitch API calls, validation, and database operations.
 * Useful for improving response times and handling API rate limits.
 */
class ProcessClipSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $twitchClipId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ClipApiService $clipApiService, GameApiService $gameService, TwitchTokenManager $tokenManager): void
    {
        // Reload user from database to get tokens (they're hidden from serialization)
        $user = User::find($this->user->id);

        try {
            // Get access token for the user
            $accessToken = $tokenManager->getValidAccessToken($user);

            // Validate the clip exists and get its data from Twitch
            $clipData = $clipApiService->getClip($this->twitchClipId, $accessToken);

            if (! $clipData) {
                Log::warning('Clip not found on Twitch', [
                    'user_id'        => $this->user->id,
                    'twitch_clip_id' => $this->twitchClipId,
                ]);

                return;
            }

            // Check if clip already exists
            $existingClip = Clip::where('twitch_clip_id', $this->twitchClipId)->first();
            if ($existingClip) {
                Log::info('Clip already exists, skipping submission', [
                    'user_id'          => $this->user->id,
                    'twitch_clip_id'   => $this->twitchClipId,
                    'existing_clip_id' => $existingClip->id,
                ]);

                return;
            }

            // Find the broadcaster user
            $broadcaster = User::where('twitch_id', $clipData->broadcasterId)->first();
            if (! $broadcaster) {
                Log::warning('Broadcaster not registered', [
                    'user_id'               => $this->user->id,
                    'twitch_clip_id'        => $this->twitchClipId,
                    'broadcaster_twitch_id' => $clipData->broadcasterId,
                ]);

                return;
            }

            // Check if user can submit clips for this broadcaster
            if (! $this->user->canSubmitClipsFor($broadcaster)) {
                Log::warning('User cannot submit clips for broadcaster', [
                    'user_id'        => $this->user->id,
                    'broadcaster_id' => $broadcaster->id,
                    'twitch_clip_id' => $this->twitchClipId,
                ]);

                return;
            }

            DB::beginTransaction();

            try {
                // Get or create game if available
                $game = null;
                if ($clipData->gameId) {
                    $gameDTO = $gameService->getGame($clipData->gameId, $accessToken);
                    if ($gameDTO) {
                        $game = \App\Models\Game::findOrCreateFromTwitch([
                            'id'          => $gameDTO->id,
                            'name'        => $gameDTO->name,
                            'box_art_url' => $gameDTO->boxArtUrl,
                            'igdb_id'     => null,
                        ]);
                    }
                }

                // Create the clip with race condition protection
                try {
                    $clip = Clip::create([
                        'submitter_id'         => $this->user->id,
                        'submitted_at'         => now(),
                        'twitch_clip_id'       => $this->twitchClipId,
                        'title'                => $clipData->title,
                        'description'          => null, // DTO has no description
                        'url'                  => $clipData->url,
                        'thumbnail_url'        => $clipData->thumbnailUrl,
                        'local_thumbnail_path' => null, // Will be set after download
                        'duration'             => $clipData->duration,
                        'view_count'           => $clipData->viewCount,
                        'created_at_twitch'    => $clipData->createdAt,
                        'clip_creator_name'    => $clipData->creatorName,
                        'broadcaster_id'       => $broadcaster->id,
                        'game_id'              => $game?->id,
                        'tags'                 => $this->extractTags($clipData),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle race condition - another request created the clip
                    if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                        Log::info('Clip already exists (race condition handled)', [
                            'user_id'        => $this->user->id,
                            'twitch_clip_id' => $this->twitchClipId,
                        ]);
                        DB::rollBack();
                        Cache::forget("processing_clip_{$this->twitchClipId}");

                        return;
                    }
                    throw $e;
                }

                Log::info('Clip created via job', [
                    'clip_id'        => $clip->id,
                    'user_id'        => $this->user->id,
                    'twitch_clip_id' => $this->twitchClipId,
                ]);

                DB::commit();

                // Dispatch thumbnail download job
                if ($clipData->thumbnailUrl) {
                    $thumbnailPath = 'clips/thumbnails/'.$clip->id.'.jpg';
                    \App\Jobs\DownloadTwitchImage::dispatch($clipData->thumbnailUrl, $thumbnailPath, 'thumbnail', $clip->id);
                }

                // Dispatch event for notifications and further processing
                \App\Events\ClipSubmitted::dispatch($clip, $this->user);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create clip in job', [
                    'user_id'        => $this->user->id,
                    'twitch_clip_id' => $this->twitchClipId,
                    'error'          => $e->getMessage(),
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            // Clear processing cache on failure
            Cache::forget("processing_clip_{$this->twitchClipId}");

            Log::error('Clip submission job failed', [
                'user_id'        => $this->user->id,
                'twitch_clip_id' => $this->twitchClipId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }

        // Clear processing cache on success
        Cache::forget("processing_clip_{$this->twitchClipId}");
    }

    /**
     * Extract tags from clip data.
     */
    private function extractTags(\App\Services\Twitch\DTOs\ClipDTO $clipData): array
    {
        $tags = [];

        // Extract broadcaster name
        if ($clipData->broadcasterName) {
            $tags[] = $clipData->broadcasterName;
        }

        // Extract language if available
        if ($clipData->language) {
            $tags[] = $clipData->language;
        }

        return array_unique($tags);
    }
}
