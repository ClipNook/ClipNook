<?php

declare(strict_types=1);

namespace App\Models\Concerns\User;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

use function __;
use function fclose;
use function filter_var;
use function fwrite;
use function getimagesize;
use function in_array;
use function logger;
use function now;
use function parse_url;
use function str_contains;
use function str_starts_with;
use function stream_get_meta_data;
use function strlen;
use function strtolower;
use function tmpfile;

use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_VALIDATE_IP;
use const FILTER_VALIDATE_URL;

/**
 * Handles user avatar management.
 *
 * This trait manages user avatars by storing them locally without database fields.
 * Priority: Custom uploaded > Twitch synced > Default avatar
 */
trait HasAvatar
{
    /**
     * Maximum allowed file size for avatars (2MB).
     */
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    /**
     * Maximum allowed image dimensions for avatars (250x250px).
     */
    private const MAX_IMAGE_DIMENSION = 250;

    /**
     * Rate limiting: Maximum attempts per time window.
     */
    private const RATE_LIMIT_MAX_ATTEMPTS = 3;

    /**
     * Rate limiting: Time window in minutes (2 hours = 120 minutes).
     */
    private const RATE_LIMIT_WINDOW_MINUTES = 120;

    /**
     * Allowed MIME types for avatar uploads.
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Allowed image extensions.
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Get the avatar source type (custom or twitch).
     */
    public function getAvatarSourceAttribute(): string
    {
        if ($this->hasAvatar()) {
            return asset('storage/'.$this->getAvatarStoragePath());
        }

        return '/images/avatar-default.svg';
    }

    /**
     * Check if user has a custom uploaded avatar.
     */
    public function hasCustomAvatar(): bool
    {
        return $this->hasAvatar();
    }

    /**
     * Get the custom avatar path (for backwards compatibility).
     */
    public function getCustomAvatarPathAttribute(): ?string
    {
        if ($this->hasAvatar()) {
            return $this->getAvatarStoragePath();
        }

        return null;
    }

    /**
     * Check if user has a custom avatar (uploaded or synced).
     */
    public function hasAvatar(): bool
    {
        return Storage::disk('public')->exists($this->getAvatarStoragePath());
    }

    /**
     * Delete the user's avatar file.
     */
    public function deleteAvatar(): bool
    {
        if (Storage::disk('public')->exists($this->getAvatarStoragePath())) {
            return Storage::disk('public')->delete($this->getAvatarStoragePath());
        }

        return false;
    }

    /**
     * Reset avatar to default (delete any custom/synced avatar).
     */
    public function resetAvatar(): bool
    {
        return $this->deleteAvatar();
    }

    /**
     * Upload and store a custom avatar from file upload.
     *
     * @throws InvalidArgumentException
     */
    public function uploadCustomAvatar(UploadedFile $file): bool
    {
        // Validate file upload
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload.');
        }

        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException('File size exceeds maximum allowed size of 2MB.');
        }

        // Validate MIME type
        $mimeType = $file->getMimeType();
        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        // Validate file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new InvalidArgumentException('Invalid file extension.');
        }

        // Additional security: Check if file is actually an image
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            throw new InvalidArgumentException('Uploaded file is not a valid image.');
        }

        // Validate image dimensions (prevent extremely large images)
        if ($imageInfo[0] > self::MAX_IMAGE_DIMENSION || $imageInfo[1] > self::MAX_IMAGE_DIMENSION) {
            throw new InvalidArgumentException('Avatar image dimensions are too large. Maximum allowed is '.self::MAX_IMAGE_DIMENSION.'x'.self::MAX_IMAGE_DIMENSION.' pixels.');
        }

        // Delete existing avatar
        $this->deleteAvatar();

        // Store new avatar with secure filename
        $file->storeAs('avatars', $this->getAvatarFilename(), 'public');

        return true;
    }

    /**
     * Download and store avatar from Twitch URL.
     *
     * @throws InvalidArgumentException
     */
    public function syncTwitchAvatar(string $twitchAvatarUrl): bool
    {
        // Validate URL format and security
        if (! $this->isValidAvatarUrl($twitchAvatarUrl)) {
            throw new InvalidArgumentException('Invalid avatar URL provided.');
        }

        // Check rate limiting
        if ($this->isRateLimitExceeded()) {
            throw new InvalidArgumentException(__('user.validation.avatar_sync_limit'));
        }

        try {
            // Download with security measures
            $response = Http::timeout(15)
                ->withOptions([
                    'verify'          => true, // SSL verification
                    'allow_redirects' => [
                        'max'       => 3, // Limit redirects
                        'strict'    => true,
                        'referer'   => false,
                        'protocols' => ['https'], // Only HTTPS
                    ],
                ])
                ->get($twitchAvatarUrl);

            if (! $response->successful()) {
                throw new InvalidArgumentException('Failed to download avatar from Twitch.');
            }

            $content       = $response->body();
            $contentLength = strlen($content);

            // Validate content size
            if ($contentLength > self::MAX_FILE_SIZE) {
                throw new InvalidArgumentException('Downloaded avatar exceeds maximum allowed size.');
            }

            // Validate that downloaded content is actually an image
            $tempFile = tmpfile();
            if ($tempFile === false) {
                throw new InvalidArgumentException('Unable to process downloaded avatar.');
            }

            $tempPath = stream_get_meta_data($tempFile)['uri'];
            fwrite($tempFile, $content);

            $imageInfo = @getimagesize($tempPath);
            fclose($tempFile);

            if ($imageInfo === false) {
                throw new InvalidArgumentException('Downloaded content is not a valid image.');
            }

            // Validate image dimensions (prevent extremely large images)
            if ($imageInfo[0] > self::MAX_IMAGE_DIMENSION || $imageInfo[1] > self::MAX_IMAGE_DIMENSION) {
                throw new InvalidArgumentException('Avatar image dimensions are too large. Maximum allowed is '.self::MAX_IMAGE_DIMENSION.'x'.self::MAX_IMAGE_DIMENSION.' pixels.');
            }

            // Delete existing avatar
            $this->deleteAvatar();

            // Store new avatar
            Storage::disk('public')->put($this->getAvatarStoragePath(), $content);

            // Record successful sync attempt for rate limiting
            $this->recordSyncAttempt();

            return true;
        } catch (Exception $e) {
            // Log the error for debugging but don't expose internal details
            logger()->warning('Avatar sync failed', [
                'user_id' => $this->id,
                'url'     => $twitchAvatarUrl,
                'error'   => $e->getMessage(),
            ]);

            throw new InvalidArgumentException('Failed to sync avatar from Twitch. Please try again later.');
        }
    }

    /**
     * Get the storage path for the avatar file.
     */
    public function getAvatarStoragePath(): string
    {
        return "avatars/{$this->getAvatarFilename()}";
    }

    /**
     * Check if the user has exceeded the rate limit for Twitch avatar sync.
     *
     * @return bool true if rate limit is exceeded, false otherwise
     */
    private function isRateLimitExceeded(): bool
    {
        $cacheKey = "avatar_sync_{$this->id}";
        $attempts = Cache::get($cacheKey, 0);

        return $attempts >= self::RATE_LIMIT_MAX_ATTEMPTS;
    }

    /**
     * Record a sync attempt for rate limiting.
     */
    private function recordSyncAttempt(): void
    {
        $cacheKey = "avatar_sync_{$this->id}";
        $attempts = Cache::get($cacheKey, 0) + 1;

        Cache::put($cacheKey, $attempts, now()->addMinutes(self::RATE_LIMIT_WINDOW_MINUTES));
    }

    /**
     * Validate if the provided URL is safe for avatar download.
     */
    private function isValidAvatarUrl(string $url): bool
    {
        // Basic URL validation
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            logger()->info('Avatar URL validation failed: Invalid URL format', ['url' => $url]);

            return false;
        }

        // Only allow HTTPS URLs
        if (! str_starts_with($url, 'https://')) {
            logger()->info('Avatar URL validation failed: Not HTTPS', ['url' => $url]);

            return false;
        }

        // Prevent access to internal/private networks
        $parsedUrl = parse_url($url);
        if (! isset($parsedUrl['host'])) {
            logger()->info('Avatar URL validation failed: No host', ['url' => $url]);

            return false;
        }

        $host = strtolower($parsedUrl['host']);

        // Block localhost and private IP ranges
        if ($host === 'localhost' || $host === '127.0.0.1' || str_starts_with($host, '127.')) {
            logger()->info('Avatar URL validation failed: Localhost/private IP', ['url' => $url]);

            return false;
        }

        // Block private IP ranges (only if host is an IP address)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                logger()->info('Avatar URL validation failed: Private/reserved IP', ['url' => $url]);

                return false;
            }
        }

        // Allow domains that contain Twitch-related keywords or are known CDNs
        $allowedPatterns = [
            'jtvnw.net',
            'twitch.tv',
            'jsdelivr.net',
            'cdn.',
        ];

        $domainAllowed = false;
        foreach ($allowedPatterns as $pattern) {
            if (str_contains($host, $pattern)) {
                $domainAllowed = true;

                break;
            }
        }

        // Also allow any HTTPS domain that doesn't appear to be internal/private
        if (! $domainAllowed) {
            // Additional check: if it's a valid domain and not obviously internal
            $domainAllowed = ! str_contains($host, 'local')
                           && ! str_contains($host, 'internal')
                           && ! str_contains($host, 'private')
                           && strlen($host) > 3; // Basic domain length check
        }

        if (! $domainAllowed) {
            logger()->info('Avatar URL validation failed: Domain not allowed', ['url' => $url, 'host' => $host]);
        }

        return $domainAllowed;
    }

    /**
     * Get the filename for the avatar.
     */
    private function getAvatarFilename(): string
    {
        return "user_{$this->id}.jpg";
    }
}
