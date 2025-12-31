<?php

namespace App\Actions\Clip;

use App\Jobs\ProcessClipSubmission;
use App\Models\Clip;
use App\Models\User;
use App\Services\Twitch\TwitchApiClient;
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
     * This action handles the initial clip submission validation and dispatches
     * a background job for processing. This improves response times and handles
     * API rate limits gracefully.
     *
     * @param  User  $user  The user submitting the clip
     * @param  string  $twitchClipId  The Twitch clip ID
     * @return bool True if job was dispatched successfully
     *
     * @throws ValidationException When validation fails
     */
    public function execute(User $user, string $twitchClipId): bool
    {
        // Basic validation - check if clip exists on Twitch
        $clipData = $this->twitchApiClient->getClip($twitchClipId);

        if (! $clipData) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['Clip not found on Twitch or access denied.']]);
        }

        // Check if clip already exists
        $existingClip = Clip::where('twitch_clip_id', $twitchClipId)->first();
        if ($existingClip) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['This clip has already been submitted.']]);
        }

        // Find the broadcaster user
        $broadcaster = User::where('twitch_id', $clipData['broadcaster_id'])->first();
        if (! $broadcaster) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['The broadcaster of this clip is not registered on our platform.']]);
        }

        // Check if user can submit clips for this broadcaster
        if (! $user->canSubmitClipsFor($broadcaster)) {
            throw ValidationException::withMessages(['twitch_clip_id' => ['You do not have permission to submit clips for this broadcaster.']]);
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
}
