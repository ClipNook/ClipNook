<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\DTOs\ClipDTO;
use App\Services\Twitch\Exceptions\TwitchApiException;

/**
 * Service for fetching Twitch clip data.
 */
class ClipApiService
{
    public function __construct(
        private readonly TwitchApiClientInterface $apiClient,
        private readonly DataSanitizerService $sanitizer,
        private readonly TwitchTokenManager $tokenManager,
    ) {}

    /**
     * Get a single clip by ID.
     */
    public function getClip(string $clipId, ?string $accessToken = null): ?ClipDTO
    {
        try {
            if ($accessToken) {
                $data = $this->apiClient->makeAuthenticatedRequest('clips', ['id' => $clipId], $accessToken);
            } else {
                $token = $this->tokenManager->getAppAccessToken();
                $data  = $this->apiClient->makeRequest('clips', ['id' => $clipId], $token);
            }
            $clips = $data['data'] ?? [];

            if (empty($clips)) {
                return null;
            }

            return $this->createClipDTO($clips[0]);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Get multiple clips by IDs.
     *
     * @return ClipDTO[]
     */
    public function getClips(array $clipIds, ?string $accessToken = null): array
    {
        try {
            $data  = $this->apiClient->makeAuthenticatedRequest('clips', ['id' => $clipIds], $accessToken);
            $clips = $data['data'] ?? [];

            return array_map([$this, 'createClipDTO'], $clips);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Create ClipDTO from API response data.
     */
    private function createClipDTO(array $data): ClipDTO
    {
        return new ClipDTO(
            id: $data['id'],
            url: $this->sanitizer->sanitizeUrl($data['url']),
            embedUrl: $this->sanitizer->sanitizeUrl($data['embed_url']),
            broadcasterId: $data['broadcaster_id'],
            broadcasterName: $this->sanitizer->sanitizeText($data['broadcaster_name']),
            creatorId: $data['creator_id'],
            creatorName: $this->sanitizer->sanitizeText($data['creator_name']),
            videoId: $data['video_id'],
            gameId: $data['game_id'],
            language: $data['language'],
            title: $this->sanitizer->sanitizeText($data['title']),
            viewCount: $this->sanitizer->sanitizeInt($data['view_count'], 0),
            createdAt: $data['created_at'],
            thumbnailUrl: $this->sanitizer->sanitizeUrl($data['thumbnail_url']),
            duration: $data['duration'],
            vodOffset: $data['vod_offset'] ?? null,
            isFeatured: $data['is_featured'],
        );
    }
}
