<?php

namespace App\Services\Twitch;

use App\Models\User;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\Exceptions\ValidationException;
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
        // Start operation; use debug level to avoid noisy info logs with PII
        Log::debug('Starting avatar restoration from Twitch');

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
            // No avatar available on Twitch; record as debug to reduce noise
            Log::debug('No profile image URL available on Twitch');
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
        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->ok()) {
                throw new \Exception('Failed to download avatar: HTTP '.$response->status());
            }

            $extension = $this->detectExtension($response, $url);
            $filename  = $this->generateFilename($user, $extension);

            Storage::disk('public')->put($filename, $response->body());

            $user->twitch_avatar = $filename;
            $user->save();

            // Successful restore (debug level; avoid PII)
            Log::debug('Avatar restored locally');

        } catch (\Throwable $e) {
            // Keep error details minimal and avoid logging PII such as URLs or user ids
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

        // Storing remote avatar URL; record at debug level only
        Log::debug('Avatar restored as remote URL');
    }

    /**
     * Detect file extension from response or URL
     */
    private function detectExtension($response, string $url): string
    {
        // Try to get from Content-Type header
        $contentType = $response->header('Content-Type');

        if ($contentType && str_contains($contentType, '/')) {
            [$type, $subtype] = explode('/', $contentType, 2);

            // Map common MIME types to extensions
            $mimeMap = [
                'jpeg' => 'jpg',
                'png'  => 'png',
                'gif'  => 'gif',
                'webp' => 'webp',
            ];

            if (isset($mimeMap[$subtype])) {
                return $mimeMap[$subtype];
            }

            if ($subtype && strlen($subtype) <= 4) {
                return $subtype;
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
    private function generateFilename(User $user, string $extension): string
    {
        $identifier = $user->twitch_id ?? $user->id ?? uniqid();

        return sprintf('avatars/%s.%s', $identifier, $extension);
    }
}
