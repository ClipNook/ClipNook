<?php

namespace App\Services\Twitch;

use App\Models\User;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\Exceptions\ValidationException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function __construct(
        private readonly OAuthInterface $oauth
    ) {}

    /**
     * Restore user avatar from Twitch.
     *
     * @throws \Exception
     */
    public function restoreFromTwitch(User $user, string $accessToken): void
    {
        try {
            // Fetch Twitch user data
            $twitchUser = $this->oauth->getUserById($accessToken, $user->twitch_id);
        } catch (\Throwable $e) {
            // Keep error message but avoid including user-specific data
            Log::error('Failed to fetch twitch user data', ['error' => $e->getMessage()]);
            throw new \Exception('Unable to fetch Twitch user data');
        }

        $profileUrl = $twitchUser->profileImageUrl ?? null;

        if (empty($profileUrl)) {
            throw new ValidationException('No profile image URL found');
        }

        // Check if we should store avatars locally
        $storeLocally = config('services.twitch.privacy.store_avatars', true);

        if ($storeLocally) {
            $this->downloadAndStoreAvatar($user, $profileUrl);
        } else {
            $this->storeRemoteUrl($user, $profileUrl);
        }
    }

    /**
     * Download avatar and store locally.
     */
    private function downloadAndStoreAvatar(User $user, string $url): void
    {
        // Basic SSRF protection: resolve host and ensure it's not a private/reserved IP
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            throw new ValidationException('Invalid avatar URL');
        }

        $resolved = gethostbyname($host);
        if (filter_var($resolved, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            Log::warning('Avatar URL resolves to private or reserved IP', ['host' => $host, 'resolved' => $resolved]);
            throw new ValidationException('Avatar URL is not allowed');
        }

        try {
            /** @var Response $response */
            $response = Http::timeout(10)->get($url);

            if (! $response->ok()) {
                throw new \Exception('Failed to download avatar: HTTP '.$response->status());
            }

            // Content-Type validation
            $contentType = $response->header('Content-Type') ?? '';
            if (! $this->isAllowedContentType($contentType)) {
                throw new ValidationException('Unsupported avatar content type');
            }

            // Size check (prefer header when available)
            $maxBytes      = (int) config('services.twitch.privacy.avatar_max_bytes', 2097152);
            $contentLength = $response->header('Content-Length');
            if ($contentLength !== null && (int) $contentLength > $maxBytes) {
                throw new ValidationException('Avatar image too large');
            }

            $body = $response->body();
            if (strlen($body) > $maxBytes) {
                throw new ValidationException('Avatar image too large');
            }

            // Determine extension and destination
            $extension = $this->detectExtension($response, $url);
            $filename  = $this->generateFilename($user, $extension, 'twitch');

            Storage::disk('public')->put($filename, $body);

            // Create thumbnail (best-effort)
            $thumbnailPath = $this->createAvatarThumbnail($filename);

            // Persist twitch avatar info
            $user->twitch_avatar = $filename;
            // Keep a thumbnail field if present (for UI); store under custom_avatar_thumbnail_path for now
            $user->custom_avatar_thumbnail_path = $thumbnailPath;
            $user->avatar_source                = 'twitch';
            $user->save();

        } catch (ValidationException $e) {
            // Re-throw validation exceptions (expected failure modes)
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Avatar download failed', ['error' => $e->getMessage()]);

            throw new \Exception('Failed to download avatar image');
        }
    }

    /**
     * Store remote avatar URL without downloading.
     */
    private function storeRemoteUrl(User $user, string $url): void
    {
        $user->twitch_avatar = $url;
        $user->save();
    }

    /**
     * Detect file extension from response or URL
     */
    private function isAllowedContentType(string $contentType): bool
    {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        $type = strtolower(trim(explode(';', $contentType)[0]));

        return in_array($type, $allowed, true);
    }

    private function detectExtension($response, string $url): string
    {
        // Try to get from Content-Type header
        $contentType = $response->header('Content-Type') ?? '';

        if ($contentType && str_contains($contentType, '/')) {
            [$type, $subtype] = explode('/', $contentType, 2);

            // Map common MIME types to extensions
            $mimeMap = [
                'jpeg' => 'jpg',
                'jpg'  => 'jpg',
                'png'  => 'png',
                'gif'  => 'gif',
                'webp' => 'webp',
            ];

            $sub = strtolower($subtype);
            if (isset($mimeMap[$sub])) {
                return $mimeMap[$sub];
            }

            // subtype may contain charset etc. split
            $subParts = preg_split('/[;\s]/', $sub);
            if ($subParts && isset($mimeMap[$subParts[0]])) {
                return $mimeMap[$subParts[0]];
            }

            if ($subParts && strlen($subParts[0]) <= 4) {
                return $subParts[0];
            }
        }

        // Fallback: try to extract from URL
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        if (! empty($pathInfo['extension'])) {
            return strtolower($pathInfo['extension']);
        }

        // Default fallback
        return 'jpg';
    }

    /**
     * Generate unique filename for avatar
     */
    private function generateFilename(User $user, string $extension, string $prefix = 'avatars'): string
    {
        // Use a non-guessable filename to avoid enumeration
        $random     = bin2hex(random_bytes(8));
        $identifier = $user->twitch_id ?? $user->id ?? uniqid();
        $hash       = hash('sha256', "{$identifier}@{$random}");

        // Include a subdirectory for twitch vs custom to keep storage tidy
        $subdir = $prefix === 'twitch' ? 'twitch' : 'custom';

        return sprintf('avatars/%s/%s.%s', $subdir, $hash, $extension);
    }

    /**
     * Create a 100x100 thumbnail for an avatar path on the public disk.
     * Returns the thumbnail path relative to the public disk, or null on failure.
     */
    private function createAvatarThumbnail(string $originalPath): ?string
    {
        // Use Storage to read the file so it works with Storage::fake in tests
        if (! Storage::disk('public')->exists($originalPath)) {
            Log::warning('Avatar source file not found for thumbnail', ['path' => $originalPath]);

            return null;
        }

        // Try to use GD to generate a thumbnail
        if (! function_exists('imagecreatefromstring')) {
            Log::warning('GD not available to create thumbnails');

            return null;
        }

        $contents = Storage::disk('public')->get($originalPath);
        if ($contents === false) {
            Log::warning('Could not read avatar file for thumbnail', ['path' => $originalPath]);

            return null;
        }

        // Build thumbnail path early
        $thumbPath = dirname($originalPath).'/thumbnails/'.basename($originalPath, '.'.pathinfo($originalPath, PATHINFO_EXTENSION)).'.png';

        $srcImage = @imagecreatefromstring($contents);
        if (! $srcImage) {
            // Fallback: if we cannot create an image resource (e.g., missing GD support), try to copy the original as a thumbnail
            Log::warning('Failed to create image resource from avatar contents; falling back to raw copy', ['path' => $originalPath]);
            Storage::disk('public')->put($thumbPath, $contents);

            return $thumbPath;
        }

        $width  = imagesx($srcImage);
        $height = imagesy($srcImage);

        $thumbSize = 100;

        // Calculate crop for center-square
        if ($width > $height) {
            $srcW = $height;
            $srcH = $height;
            $srcX = (int) floor(($width - $height) / 2);
            $srcY = 0;
        } else {
            $srcW = $width;
            $srcH = $width;
            $srcX = 0;
            $srcY = (int) floor(($height - $width) / 2);
        }

        $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
        // Preserve transparency
        imagesavealpha($thumb, true);
        $trans_colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $trans_colour);

        imagecopyresampled(
            $thumb,
            $srcImage,
            0, 0,
            $srcX, $srcY,
            $thumbSize, $thumbSize,
            $srcW, $srcH
        );

        ob_start();
        // Save as PNG for maximum compatibility
        imagepng($thumb);
        $pngData = ob_get_clean();

        imagedestroy($thumb);
        imagedestroy($srcImage);

        if ($pngData === false || $pngData === null) {
            Log::warning('Failed to generate thumbnail PNG', ['path' => $originalPath]);

            return null;
        }

        Storage::disk('public')->put($thumbPath, $pngData);

        return $thumbPath;
    }
}
