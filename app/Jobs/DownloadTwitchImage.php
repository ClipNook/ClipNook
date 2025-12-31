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

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, string $savePath, string $type = 'thumbnail')
    {
        $this->url      = $url;
        $this->savePath = $savePath;
        $this->type     = $type;
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
