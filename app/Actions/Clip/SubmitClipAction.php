<?php

namespace App\Actions\Clip;

use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\TwitchApiClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubmitClipAction
{
    public function __construct(
        private TwitchApiClient $twitchApiClient
    ) {}

    /**
     * Submit a clip from Twitch
     *
     * @param  User  $user  The user submitting the clip
     * @param  string  $twitchClipId  The Twitch clip ID
     * @return Clip The created clip
     *
     * @throws ValidationException
     */
    public function execute(User $user, string $twitchClipId): Clip
    {
        // Validate the clip exists and get its data from Twitch
        $clipData = $this->twitchApiClient->getClip($twitchClipId);

        if (! $clipData) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['Clip not found on Twitch or access denied.']]);
        }

        // Check if clip already exists
        $existingClip = Clip::where('twitch_clip_id', $twitchClipId)->first();
        if ($existingClip) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['This clip has already been submitted.']]);
        }

        // Validate clip ownership (user must be the creator)
        if ($clipData['broadcaster_id'] !== $user->twitch_id) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['You can only submit clips that you created.']]);
        }

        DB::beginTransaction();

        try {
            // Create the clip
            $clip = Clip::create([
                'user_id'           => $user->id,
                'twitch_clip_id'    => $twitchClipId,
                'title'             => $clipData['title'],
                'description'       => $clipData['description'] ?? null,
                'url'               => $clipData['url'],
                'thumbnail_url'     => $clipData['thumbnail_url'],
                'duration'          => $clipData['duration'],
                'view_count'        => $clipData['view_count'],
                'created_at_twitch' => $clipData['created_at'],
                'tags'              => $this->extractTags($clipData),
            ]);

            // Log the activity
            Log::info('Clip submitted', [
                'user_id'        => $user->id,
                'clip_id'        => $clip->id,
                'twitch_clip_id' => $twitchClipId,
            ]);

            DB::commit();

            return $clip;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit clip', [
                'user_id'        => $user->id,
                'twitch_clip_id' => $twitchClipId,
                'error'          => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Extract tags from clip data
     */
    private function extractTags(array $clipData): array
    {
        $tags = [];

        // Extract game name if available
        if (isset($clipData['game_name'])) {
            $tags[] = $clipData['game_name'];
        }

        // Extract broadcaster name
        if (isset($clipData['broadcaster_name'])) {
            $tags[] = $clipData['broadcaster_name'];
        }

        // Extract language if available
        if (isset($clipData['language'])) {
            $tags[] = $clipData['language'];
        }

        return array_unique($tags);
    }
}
