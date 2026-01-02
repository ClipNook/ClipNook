<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Twitch\Contracts\DownloadInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

use function __;
use function dirname;

final class DownloadTwitchImage implements ShouldQueue
{
    use Queueable;

    protected string $url;

    protected string $savePath;

    protected string $type; // 'thumbnail', 'profile', or 'box_art'

    protected ?int $gameId; // Optional game ID for updating box art path

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, string $savePath, string $type = 'thumbnail', ?int $clipId = null, ?int $gameId = null)
    {
        $this->url      = $url;
        $this->savePath = $savePath;
        $this->type     = $type;
        $this->clipId   = $clipId;
        $this->gameId   = $gameId;
    }

    /**
     * Execute the job.
     */
    public function handle(DownloadInterface $downloader): void
    {
        try {
            // Ensure directory exists
            $directory = dirname($this->savePath);
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);

            if ($this->type === 'thumbnail') {
                $downloader->downloadThumbnail($this->url, $this->savePath);
                Log::info(__('twitch.download_thumbnail_success', ['url' => $this->url, 'path' => $this->savePath]));

                // Update clip with local thumbnail path if clip ID provided
                if ($this->clipId) {
                    \App\Models\Clip::where('id', $this->clipId)->update(['local_thumbnail_path' => $this->savePath]);
                }
            } elseif ($this->type === 'profile') {
                $downloader->downloadProfileImage($this->url, $this->savePath);
                Log::info(__('twitch.download_profile_success', ['url' => $this->url, 'path' => $this->savePath]));
            } elseif ($this->type === 'box_art') {
                $downloader->downloadBoxArt($this->url, $this->savePath);
                Log::info(__('twitch.download_box_art_success', ['url' => $this->url, 'path' => $this->savePath]));

                // Update game with local box art path if game ID provided
                if ($this->gameId) {
                    \App\Models\Game::where('id', $this->gameId)->update(['local_box_art_path' => $this->savePath]);
                }
            }
        } catch (Exception $e) {
            Log::error(__('twitch.download_failed', ['type' => $this->type, 'error' => $e->getMessage()]));

            throw $e;
        }
    }
}
