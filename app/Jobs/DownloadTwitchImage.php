<?php

namespace App\Jobs;

use App\Services\Twitch\Contracts\DownloadInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DownloadTwitchImage implements ShouldQueue
{
    use Queueable;

    protected string $url;

    protected string $savePath;

    protected string $type; // 'thumbnail' or 'profile'

    protected ?int $clipId; // Optional clip ID for updating thumbnail path

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, string $savePath, string $type = 'thumbnail', ?int $clipId = null)
    {
        $this->url      = $url;
        $this->savePath = $savePath;
        $this->type     = $type;
        $this->clipId   = $clipId;
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
            }
        } catch (\Exception $e) {
            Log::error(__('twitch.download_failed', ['type' => $this->type, 'error' => $e->getMessage()]));
            throw $e;
        }
    }
}
