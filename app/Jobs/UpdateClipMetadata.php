<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Clip;
use App\Services\Twitch\Api\ClipApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateClipMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Clip $clip) {}

    /**
     * Execute the job.
     */
    public function handle(ClipApiService $clipApiService, TwitchTokenManager $tokenManager): void
    {
        try {
            // Get updated clip data from Twitch
            $clipData = $clipApiService->getClip($this->clip->twitch_clip_id);

            if (! $clipData) {
                Log::warning('Clip not found on Twitch during metadata update', [
                    'clip_id'        => $this->clip->id,
                    'twitch_clip_id' => $this->clip->twitch_clip_id,
                ]);

                return;
            }

            // Update clip metadata
            $this->clip->update([
                'title'         => $clipData->title,
                'description'   => $clipData->description ?? $this->clip->description,
                'thumbnail_url' => $clipData->thumbnailUrl,
                'view_count'    => $clipData->viewCount,
                'tags'          => $this->mergeTags($this->clip->tags, $clipData->tags),
            ]);

            Log::info('Clip metadata updated', [
                'clip_id'        => $this->clip->id,
                'twitch_clip_id' => $this->clip->twitch_clip_id,
                'new_view_count' => $clipData['view_count'],
            ]);
        } catch (\Exception $e) {
            Log::error('Clip metadata update job failed', [
                'clip_id'        => $this->clip->id,
                'twitch_clip_id' => $this->clip->twitch_clip_id,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Extract tags from clip data.
     */
    private function extractTags(\App\Services\Twitch\DTOs\ClipDTO $clipData): array
    {
        $tags = [];

        // Extract game name if available
        if ($clipData->gameName) {
            $tags[] = $clipData->gameName;
        }

        // Extract broadcaster name
        if ($clipData->broadcasterName) {
            $tags[] = $clipData->broadcasterName;
        }

        // Extract language if available
        if (isset($clipData['language'])) {
            $tags[] = $clipData['language'];
        }

        return array_unique($tags);
    }

    /**
     * Merge existing tags with new tags.
     */
    private function mergeTags(array $existingTags, array $newTags): array
    {
        return array_unique(array_merge($existingTags, $newTags));
    }
}
