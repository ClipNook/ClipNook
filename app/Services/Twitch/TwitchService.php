<?php

declare(strict_types=1);

namespace App\Services\Twitch;

use App\Events\TwitchTokenRefreshed;
use App\Services\Twitch\Contracts\DownloadInterface;
use App\Services\Twitch\Contracts\TwitchApiInterface;
use App\Services\Twitch\DTOs\ApiLogEntryDTO;
use App\Services\Twitch\DTOs\ClipDTO;
use App\Services\Twitch\DTOs\GameDTO;
use App\Services\Twitch\DTOs\StreamerDTO;
use App\Services\Twitch\DTOs\TokenDTO;
use App\Services\Twitch\DTOs\VideoDTO;
use App\Services\Twitch\Enums\RequestType;
use App\Services\Twitch\Exceptions\TwitchApiException;
use App\Services\Twitch\Traits\ApiCaching;
use App\Services\Twitch\Traits\ApiLogging;
use App\Services\Twitch\Traits\ApiRateLimiting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TwitchService implements DownloadInterface, TwitchApiInterface
{
    use ApiCaching, ApiLogging, ApiRateLimiting;

    protected readonly string $clientId;

    protected readonly string $clientSecret;

    protected readonly int $cacheTtl;

    protected ?string $accessToken = null;

    protected ?string $refreshToken = null;

    protected ?int $tokenExpiresAt = null;

    public function __construct(
        protected readonly TwitchApiClient $apiClient,
        protected readonly TwitchTokenManager $tokenManager,
        protected readonly TwitchDataSanitizer $sanitizer,
    ) {
        $this->clientId     = config('twitch.client_id');
        $this->clientSecret = config('twitch.client_secret');
        $this->cacheTtl     = config('twitch.cache_ttl', 3600);

        $this->validateConfiguration();
    }

    protected function validateConfiguration(): void
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw TwitchApiException::invalidConfig(__('twitch.api_invalid_config'));
        }
    }

    protected function saveTokensToSession(string $accessToken, ?string $refreshToken, ?int $expiresAt): void
    {
        session([
            'twitch_access_token'     => $accessToken,
            'twitch_refresh_token'    => $refreshToken,
            'twitch_token_expires_at' => $expiresAt,
        ]);
        $this->accessToken    = $accessToken;
        $this->refreshToken   = $refreshToken;
        $this->tokenExpiresAt = $expiresAt;
    }

    protected function loadTokensFromSession(): void
    {
        $this->accessToken    = session('twitch_access_token');
        $this->refreshToken   = session('twitch_refresh_token');
        $this->tokenExpiresAt = session('twitch_token_expires_at');
    }

    protected function loadTokensFromUser(): void
    {
        $user = auth()->user();

        if ($user && $user->twitch_access_token) {
            $this->accessToken    = $user->twitch_access_token;
            $this->refreshToken   = $user->twitch_refresh_token;
            $this->tokenExpiresAt = $user->twitch_token_expires_at?->timestamp;
        }
    }

    protected function ensureValidToken(): void
    {
        // Load tokens from authenticated user if not already loaded
        if (! $this->accessToken && ! $this->refreshToken) {
            $this->loadTokensFromUser();
        }

        if (! $this->accessToken) {
            throw TwitchApiException::authenticationRequired(__('twitch.authentication_required'));
        }

        if ($this->tokenExpiresAt && time() >= $this->tokenExpiresAt) {
            if (! $this->refreshToken) {
                throw TwitchApiException::authenticationRequired(__('twitch.token_expired_no_refresh'));
            }
            $this->refreshAccessToken();
        }
    }

    public function setAccessToken(string $token): void
    {
        $this->saveTokensToSession($token, $this->refreshToken, $this->tokenExpiresAt);
    }

    public function setTokens(TokenDTO $token): void
    {
        $expiresAt = time() + $token->expiresIn - config('twitch.token_refresh_buffer', 300);
        $this->saveTokensToSession($token->accessToken, $token->refreshToken, $expiresAt);
    }

    public function refreshAccessToken(): ?TokenDTO
    {
        if (! $this->refreshToken) {
            throw TwitchApiException::noRefreshToken();
        }

        $data = $this->tokenManager->refreshUserToken($this->refreshToken);

        $token = new TokenDTO(
            accessToken: $data['access_token'],
            refreshToken: $data['refresh_token'] ?? $this->refreshToken,
            expiresIn: $data['expires_in'],
            tokenType: $data['token_type'],
            scope: $data['scope'] ?? null,
            issuedAt: time(),
        );

        $this->setTokens($token);
        $this->logApiCall(new ApiLogEntryDTO(
            endpoint: 'https://id.twitch.tv/oauth2/token',
            params: ['grant_type' => 'refresh_token'],
            response: ['success' => true]
        ));

        // Update user tokens in database
        $user = auth()->user();
        if ($user) {
            $user->update([
                'twitch_access_token'     => $token->accessToken,
                'twitch_refresh_token'    => $token->refreshToken,
                'twitch_token_expires_at' => now()->addSeconds($token->expiresIn),
            ]);
        }

        TwitchTokenRefreshed::dispatch(auth()->id() ?? 'guest', true);

        return $token;
    }

    protected function makeApiRequest(string $endpoint, array $params, RequestType $type): ?array
    {
        $this->ensureValidToken();

        $rateLimitKey = "twitch_api_{$type->value}";
        if (! $this->checkActionRateLimit($type->value)) {
            throw TwitchApiException::rateLimitExceeded();
        }

        $cacheKey = "twitch_{$type->value}_".md5(json_encode($params));

        return $this->getCachedResponse($cacheKey, function () use ($endpoint, $params) {
            try {
                // Extract relative endpoint from full URL if provided
                $relativeEndpoint = $this->extractRelativeEndpoint($endpoint);
                $data             = $this->apiClient->makeRequest($relativeEndpoint, $params, $this->accessToken);
                $this->logApiCall(new ApiLogEntryDTO(
                    endpoint: $endpoint,
                    params: $params,
                    response: $data
                ));

                return $data['data'] ?? null;
            } catch (TwitchApiException $e) {
                if (str_contains($e->getMessage(), '401')) {
                    // Token expired, try refresh once
                    $this->refreshAccessToken();
                    $relativeEndpoint = $this->extractRelativeEndpoint($endpoint);
                    $data             = $this->apiClient->makeRequest($relativeEndpoint, $params, $this->accessToken);
                    $this->logApiCall(new ApiLogEntryDTO(
                        endpoint: $endpoint,
                        params: $params,
                        response: $data
                    ));

                    return $data['data'] ?? null;
                }

                $this->logApiCall(new ApiLogEntryDTO(
                    endpoint: $endpoint,
                    params: $params,
                    error: $e->getMessage()
                ));
                throw $e;
            }
        }, $this->cacheTtl);
    }

    /**
     * Extract relative endpoint from full Twitch API URL.
     */
    protected function extractRelativeEndpoint(string $endpoint): string
    {
        if (str_starts_with($endpoint, 'https://api.twitch.tv/helix/')) {
            return substr($endpoint, strlen('https://api.twitch.tv/helix/'));
        }

        // If it's already relative, return as-is
        return $endpoint;
    }

    public function getClip(string $clipId): ?ClipDTO
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/clips', ['id' => $clipId], RequestType::CLIP);

        // Twitch API returns an array even for single items, take the first one
        $clipData = $data && is_array($data) && count($data) > 0 ? $data[0] : null;

        return $clipData ? new ClipDTO(
            id: $clipData['id'],
            url: $this->sanitizer->sanitizeUrl($clipData['url']),
            embedUrl: $this->sanitizer->sanitizeUrl($clipData['embed_url']),
            broadcasterId: $clipData['broadcaster_id'],
            broadcasterName: $this->sanitizer->sanitizeText($clipData['broadcaster_name']),
            creatorId: $clipData['creator_id'],
            creatorName: $this->sanitizer->sanitizeText($clipData['creator_name']),
            videoId: $clipData['video_id'],
            gameId: $clipData['game_id'],
            language: $clipData['language'],
            title: $this->sanitizer->sanitizeText($clipData['title']),
            viewCount: $this->sanitizer->sanitizeInt($clipData['view_count'], 0),
            createdAt: $clipData['created_at'],
            thumbnailUrl: $this->sanitizer->sanitizeUrl($clipData['thumbnail_url']),
            duration: $clipData['duration'],
            vodOffset: $clipData['vod_offset'] ?? null,
            isFeatured: $clipData['is_featured'],
        ) : null;
    }

    public function getGame(string $gameId): ?GameDTO
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/games', ['id' => $gameId], RequestType::GAME);

        // Twitch API returns an array even for single items, take the first one
        $gameData = $data && is_array($data) && count($data) > 0 ? $data[0] : null;

        return $gameData ? new GameDTO(
            id: $gameData['id'],
            name: $this->sanitizer->sanitizeText($gameData['name']),
            boxArtUrl: $this->sanitizer->sanitizeUrl($gameData['box_art_url']),
            igdbId: $gameData['igdb_id'] ?? null,
        ) : null;
    }

    public function getStreamer(string $userId): ?StreamerDTO
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/users', ['id' => $userId], RequestType::STREAMER);

        if (! $data || ! is_array($data) || empty($data)) {
            return null;
        }

        $userData = $data[0];

        return new StreamerDTO(
            id: $userData['id'],
            login: $userData['login'],
            displayName: $this->sanitizer->sanitizeText($userData['display_name']),
            type: $userData['type'] ?? '',
            broadcasterType: $userData['broadcaster_type'] ?? '',
            description: $this->sanitizer->sanitizeText($userData['description'] ?? ''),
            profileImageUrl: $this->sanitizer->sanitizeUrl($userData['profile_image_url']),
            offlineImageUrl: $this->sanitizer->sanitizeUrl($userData['offline_image_url']),
            viewCount: $this->sanitizer->sanitizeInt($userData['view_count'], 0),
            createdAt: $userData['created_at'],
            email: $userData['email'] ?? null,
        );
    }

    public function getCurrentUser(): ?StreamerDTO
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/users', [], RequestType::STREAMER);

        if (! $data || ! is_array($data) || empty($data)) {
            return null;
        }

        $userData = $data[0];

        return new StreamerDTO(
            id: $userData['id'],
            login: $userData['login'],
            displayName: $this->sanitizer->sanitizeText($userData['display_name']),
            type: $userData['type'] ?? '',
            broadcasterType: $userData['broadcaster_type'] ?? '',
            description: $this->sanitizer->sanitizeText($userData['description'] ?? ''),
            profileImageUrl: $this->sanitizer->sanitizeUrl($userData['profile_image_url']),
            offlineImageUrl: $this->sanitizer->sanitizeUrl($userData['offline_image_url']),
            viewCount: $this->sanitizer->sanitizeInt($userData['view_count'], 0),
            createdAt: $userData['created_at'],
            email: $userData['email'] ?? null,
        );
    }

    public function getClips(array $clipIds): array
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/clips', ['id' => $clipIds], RequestType::CLIP);

        return array_map(fn ($item) => new ClipDTO(
            id: $item['id'],
            url: $this->sanitizer->sanitizeUrl($item['url']),
            embedUrl: $this->sanitizer->sanitizeUrl($item['embed_url']),
            broadcasterId: $item['broadcaster_id'],
            broadcasterName: $this->sanitizer->sanitizeText($item['broadcaster_name']),
            creatorId: $item['creator_id'],
            creatorName: $this->sanitizer->sanitizeText($item['creator_name']),
            videoId: $item['video_id'],
            gameId: $item['game_id'],
            language: $item['language'],
            title: $this->sanitizer->sanitizeText($item['title']),
            viewCount: $this->sanitizer->sanitizeInt($item['view_count'], 0),
            createdAt: $item['created_at'],
            thumbnailUrl: $this->sanitizer->sanitizeUrl($item['thumbnail_url']),
            duration: $item['duration'],
            vodOffset: $item['vod_offset'] ?? null,
            isFeatured: $item['is_featured'],
        ), $data ?? []);
    }

    public function getGames(array $gameIds): array
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/games', ['id' => $gameIds], RequestType::GAME);

        return array_map(fn ($item) => new GameDTO(
            id: $item['id'],
            name: $this->sanitizer->sanitizeText($item['name']),
            boxArtUrl: $this->sanitizer->sanitizeUrl($item['box_art_url']),
            igdbId: $item['igdb_id'] ?? null,
        ), $data ?? []);
    }

    public function getStreamers(array $userIds): array
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/users', ['id' => $userIds], RequestType::STREAMER);

        return array_map(fn ($item) => new StreamerDTO(
            id: $item['id'],
            login: $item['login'],
            displayName: $this->sanitizer->sanitizeText($item['display_name']),
            type: $item['type'],
            broadcasterType: $item['broadcaster_type'],
            description: $this->sanitizer->sanitizeText($item['description']),
            profileImageUrl: $this->sanitizer->sanitizeUrl($item['profile_image_url']),
            offlineImageUrl: $this->sanitizer->sanitizeUrl($item['offline_image_url']),
            viewCount: $this->sanitizer->sanitizeInt($item['view_count'], 0),
            createdAt: $item['created_at'],
        ), $data ?? []);
    }

    public function getVideo(string $videoId): ?VideoDTO
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/videos', ['id' => $videoId], RequestType::VIDEO);

        return $data ? new VideoDTO(
            id: $data['id'],
            streamId: $data['stream_id'] ?? null,
            userId: $data['user_id'],
            userLogin: $data['user_login'],
            userName: $data['user_name'],
            title: $data['title'],
            description: $data['description'],
            createdAt: $data['created_at'],
            publishedAt: $data['published_at'],
            url: $data['url'],
            thumbnailUrl: $data['thumbnail_url'],
            viewable: $data['viewable'],
            viewCount: $data['view_count'],
            language: $data['language'],
            type: $data['type'],
            duration: $data['duration'],
            mutedSegments: $data['muted_segments'] ?? null,
        ) : null;
    }

    public function getVideos(array $videoIds): array
    {
        $data = $this->makeApiRequest('https://api.twitch.tv/helix/videos', ['id' => $videoIds], RequestType::VIDEO);

        return array_map(fn ($item) => new VideoDTO(
            id: $item['id'],
            streamId: $item['stream_id'] ?? null,
            userId: $item['user_id'],
            userLogin: $item['user_login'],
            userName: $item['user_name'],
            title: $item['title'],
            description: $item['description'],
            createdAt: $item['created_at'],
            publishedAt: $item['published_at'],
            url: $item['url'],
            thumbnailUrl: $item['thumbnail_url'],
            viewable: $item['viewable'],
            viewCount: $item['view_count'],
            language: $item['language'],
            type: $item['type'],
            duration: $item['duration'],
            mutedSegments: $item['muted_segments'] ?? null,
        ), $data ?? []);
    }

    public function downloadThumbnail(string $url, string $savePath): bool
    {
        try {
            // Validate URL is HTTPS and from trusted domain
            if (! $this->isValidImageUrl($url)) {
                throw new \InvalidArgumentException('Invalid or untrusted image URL');
            }

            $image = Http::timeout(10)->retry(3, 100)->get($url)->body();

            // Check file size (max 5MB)
            if (strlen($image) > 5242880) {
                throw new \Exception('Image too large');
            }

            Storage::disk('public')->put($savePath, $image);

            return true;
        } catch (\Exception $e) {
            throw TwitchApiException::thumbnailDownloadFailed($e->getMessage());
        }
    }

    public function downloadProfileImage(string $url, string $savePath): bool
    {
        try {
            // Validate URL is HTTPS and from trusted domain
            if (! $this->isValidImageUrl($url)) {
                throw new \InvalidArgumentException('Invalid or untrusted image URL');
            }

            $image = Http::timeout(10)->retry(3, 100)->get($url)->body();

            // Check file size (max 5MB)
            if (strlen($image) > 5242880) {
                throw new \Exception('Image too large');
            }

            Storage::disk('public')->put($savePath, $image);

            return true;
        } catch (\Exception $e) {
            throw TwitchApiException::profileImageDownloadFailed($e->getMessage());
        }
    }

    /**
     * Validate if URL is HTTPS and from trusted Twitch domains
     */
    protected function isValidImageUrl(string $url): bool
    {
        $parsed = parse_url($url);

        // Must be HTTPS
        if (($parsed['scheme'] ?? '') !== 'https') {
            return false;
        }

        // Must be from trusted domains
        $trustedDomains = ['static-cdn.jtvnw.net', 'clips-media-assets2.twitch.tv'];
        $host           = $parsed['host'] ?? '';

        foreach ($trustedDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return true;
            }
        }

        return false;
    }

    public function parseClipId(string $input): ?string
    {
        // Accepts full URL or just the ID
        if (preg_match('/clip\/([\w-]+)/', $input, $matches)) {
            return $matches[1];
        }
        if (preg_match('/clips\.twitch\.tv\/([\w-]+)/', $input, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([\w-]+)$/', $input)) {
            return $input;
        }

        return null;
    }

    /**
     * Extract user ID from Twitch URL.
     */
    public static function extractUserIdFromUrl(string $url): ?string
    {
        // Parse the URL
        $parsedUrl = parse_url($url);

        // Check if it's a twitch.tv domain
        if (! isset($parsedUrl['host']) || ! str_contains($parsedUrl['host'], 'twitch.tv')) {
            return null;
        }

        // Extract path and remove leading slash
        $path = $parsedUrl['path'] ?? '';
        $path = ltrim($path, '/');

        // Get the first segment (username)
        $segments = explode('/', $path);

        return $segments[0] ?: null;
    }
}
