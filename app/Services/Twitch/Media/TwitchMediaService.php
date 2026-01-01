<?php

declare(strict_types=1);

namespace App\Services\Twitch\Media;

use App\Contracts\ImageValidatorInterface;
use App\Services\Twitch\Contracts\DownloadInterface;
use App\Services\Twitch\Exceptions\TwitchApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Service for downloading and managing Twitch media (thumbnails, avatars).
 */
class TwitchMediaService implements DownloadInterface
{
    public function __construct(
        private readonly ImageValidatorInterface $imageValidator,
    ) {}

    /**
     * Download and validate an image from a URL.
     *
     * @throws TwitchApiException
     */
    public function downloadImage(string $url, string $savePath): bool
    {
        // Validate URL security
        $this->imageValidator->validateUrl($url);

        $response = Http::timeout(config('constants.http.timeout_seconds', 30))
            ->retry(config('constants.http.retry_count', 3), config('constants.http.retry_delay_ms', 100))
            ->get($url);

        if (! $response->successful()) {
            throw new \Exception('Failed to download image: HTTP '.$response->status());
        }

        $image = $response->body();

        // Validate image MIME type
        $this->imageValidator->validateMimeType($image);

        // Validate file size
        $this->imageValidator->validateSize(
            $image,
            config('twitch.privacy.avatar_max_bytes', 2097152)
        );

        Storage::disk('public')->put($savePath, $image);

        return true;
    }

    /**
     * Download thumbnail image.
     */
    public function downloadThumbnail(string $url, string $savePath): bool
    {
        try {
            return $this->downloadImage($url, $savePath);
        } catch (\Exception $e) {
            throw TwitchApiException::thumbnailDownloadFailed($e->getMessage());
        }
    }

    /**
     * Download profile image.
     */
    public function downloadProfileImage(string $url, string $savePath): bool
    {
        try {
            return $this->downloadImage($url, $savePath);
        } catch (\Exception $e) {
            throw TwitchApiException::profileImageDownloadFailed($e->getMessage());
        }
    }

    /**
     * Validate if URL is from trusted Twitch domains.
     */
    public function isValidImageUrl(string $url): bool
    {
        $parsed = parse_url($url);

        // Must be HTTPS
        if (($parsed['scheme'] ?? '') !== 'https') {
            return false;
        }

        // Must be from trusted domains
        $trustedDomains = [
            'static-cdn.jtvnw.net',
            'clips-media-assets2.twitch.tv',
        ];

        $host = $parsed['host'] ?? '';

        foreach ($trustedDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return true;
            }
        }

        return false;
    }
}
