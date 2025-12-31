<?php

namespace App\Actions\Clip;

use App\Exceptions\BroadcasterNotRegisteredException;
use App\Exceptions\ClipNotFoundException;
use App\Exceptions\ClipPermissionException;
use App\Jobs\ProcessClipSubmission;
use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\TwitchGameService;
use App\Services\Twitch\TwitchService;
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
        // Basic validation - check if clip exists on Twitch
        $clipData = $this->twitchService->getClip($twitchClipId);

        if (! $clipData) {
            throw ClipNotFoundException::forId($twitchClipId);
        }

        // Check if clip already exists
        $existingClip = Clip::where('twitch_clip_id', $twitchClipId)->first();
        if ($existingClip) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['This clip has already been submitted.']]);
        }

        // Find the broadcaster user
        $broadcaster = User::where('twitch_id', $clipData['broadcaster_id'])->first();
        if (! $broadcaster) {
            throw BroadcasterNotRegisteredException::forTwitchId($clipData['broadcaster_id']);
        }

        // Check if user can submit clips for this broadcaster
        if (! $user->canSubmitClipsFor($broadcaster)) {
            throw ClipPermissionException::cannotSubmitForBroadcaster($broadcaster->id);
        }

        // Dispatch the background job for processing
        ProcessClipSubmission::dispatch($user, $twitchClipId);

        // Log the job dispatch
        Log::info('Clip submission job dispatched', [
            'user_id'        => $user->id,
            'twitch_clip_id' => $twitchClipId,
            'broadcaster_id' => $broadcaster->id,
        ]);

        return true;
    }

    /**
     * Submit a clip from Twitch (synchronous version for testing)
     *
     * This method performs the same validation but creates the clip synchronously
     * instead of dispatching a job. Used primarily for testing.
     *
     * @param  User  $user  The user submitting the clip
     * @param  string  $twitchClipId  The Twitch clip ID
     * @return Clip The created clip
     *
     * @throws ClipNotFoundException When clip is not found on Twitch
     * @throws BroadcasterNotRegisteredException When broadcaster is not registered
     * @throws ClipPermissionException When user lacks permission
     */
    public function executeSync(User $user, string $twitchClipId): Clip
    {
        return DB::transaction(function () use ($user, $twitchClipId) {
            // Basic validation - check if clip exists on Twitch
            $clipData = $this->twitchService->getClip($twitchClipId);

            if (! $clipData) {
                throw ClipNotFoundException::forId($twitchClipId);
            }

            // Check if clip already exists
            $existingClip = Clip::where('twitch_clip_id', $twitchClipId)->first();
            if ($existingClip) {
                throw ValidationException::withMessages(['twitch_clip_id' => ['This clip has already been submitted.']]);
            }

            // Find the broadcaster user
            $broadcaster = User::where('twitch_id', $clipData->broadcasterId)->first();
            if (! $broadcaster) {
                throw BroadcasterNotRegisteredException::forTwitchId($clipData->broadcasterId);
            }

            // Check if user can submit clips for this broadcaster
            if (! $user->canSubmitClipsFor($broadcaster)) {
                throw ClipPermissionException::cannotSubmitForBroadcaster($broadcaster->id);
            }

            // Get or create game if available
            $game = null;
            if ($clipData->gameId) {
                $game = $this->twitchGameService->getOrCreateGame($clipData->gameId);
            }

            // Create the clip synchronously for testing
            $clip = Clip::create([
                'submitter_id'      => $user->id,
                'submitted_at'      => now(),
                'twitch_clip_id'    => $twitchClipId,
                'title'             => $clipData->title,
                'description'       => null, // DTO has no description
                'url'               => $clipData->url,
                'thumbnail_url'     => $clipData->thumbnailUrl,
                'duration'          => $clipData->duration,
                'view_count'        => $clipData->viewCount,
                'created_at_twitch' => $clipData->createdAt,
                'broadcaster_id'    => $broadcaster->id,
                'game_id'           => $game?->id,
                'tags'              => $this->extractTags($clipData),
            ]);

            // Fire event for audit trails and notifications
            \App\Events\ClipSubmitted::dispatch($clip, $user);

            return $clip;
        });
    }

    /**
     * Extract tags from clip data
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
