<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\DTOs\VideoDTO;
use App\Services\Twitch\Exceptions\TwitchApiException;

/**
 * Service for fetching Twitch video data.
 */
class VideoApiService
{
    public function __construct(
        private readonly TwitchApiClientInterface $apiClient,
        private readonly DataSanitizerService $sanitizer,
        private readonly TwitchTokenManager $tokenManager,
    ) {}

    /**
     * Get a single video by ID.
     */
    public function getVideo(string $videoId, ?string $accessToken = null): ?VideoDTO
    {
        try {
            $token  = $accessToken ?? $this->tokenManager->getAppAccessToken();
            $data   = $this->apiClient->makeAuthenticatedRequest('videos', ['id' => $videoId], $token);
            $videos = $data['data'] ?? [];

            if (empty($videos)) {
                return null;
            }

            return $this->createVideoDTO($videos[0]);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Get multiple videos by IDs.
     *
     * @return VideoDTO[]
     */
    public function getVideos(array $videoIds, ?string $accessToken = null): array
    {
        try {
            $token  = $accessToken ?? $this->tokenManager->getAppAccessToken();
            $data   = $this->apiClient->makeAuthenticatedRequest('videos', ['id' => $videoIds], $token);
            $videos = $data['data'] ?? [];

            return array_map([$this, 'createVideoDTO'], $videos);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Create VideoDTO from API response data.
     */
    private function createVideoDTO(array $data): VideoDTO
    {
        return new VideoDTO(
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
        );
    }
}
