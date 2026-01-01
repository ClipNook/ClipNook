<?php

namespace App\Jobs;

use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\TwitchGameService;
use App\Services\Twitch\TwitchService;
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
        public User $user,
        public string $twitchClipId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TwitchService $twitchService, TwitchGameService $gameService): void
    {
        // Set the authenticated user for token access
        auth()->login($this->user);

        try {
            // Validate the clip exists and get its data from Twitch
            $clipData = $twitchService->getClip($this->twitchClipId);

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

            // Get creator name from DTO (Twitch API provides it)
            $creatorName = $clipData->creatorName;
            
            Log::debug('Creator name from Twitch API', [
                'creator_id' => $clipData->creatorId,
                'creator_name' => $creatorName,
                'creator_name_from_dto' => $clipData->creatorName,
            ]);

            DB::beginTransaction();

            try {
                // Get or create game if available
                $game = null;
                if ($clipData->gameId) {
                    $game = $gameService->getOrCreateGame($clipData->gameId);
                }

                Log::info('About to create clip with creator name', [
                    'creator_name' => $creatorName,
                    'creator_id' => $clipData->creatorId,
                ]);

                // Create the clip
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
                    'clip_creator_name'    => $creatorName,
                    'broadcaster_id'       => $broadcaster->id,
                    'game_id'              => $game?->id,
                    'tags'                 => $this->extractTags($clipData),
                ]);

                Log::info('Clip created via job', [
                    'clip_id'        => $clip->id,
                    'user_id'        => $this->user->id,
                    'twitch_clip_id' => $this->twitchClipId,
                ]);

                DB::commit();

                // Dispatch thumbnail download job
                if ($clipData->thumbnailUrl) {
                    $thumbnailPath = 'clips/thumbnails/'.$clip->id.'.jpg';
                    \App\Jobs\DownloadTwitchImage::dispatch($clipData->thumbnailUrl, $thumbnailPath, 'thumbnail');

                    // Update clip with local path
                    $clip->update(['local_thumbnail_path' => $thumbnailPath]);
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
