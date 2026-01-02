<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\DTOs\StreamerDTO;
use App\Services\Twitch\Exceptions\TwitchApiException;

use function array_map;

/**
 * Service for fetching Twitch streamer/user data.
 */
final class StreamerApiService
{
    public function __construct(
        private readonly TwitchApiClientInterface $apiClient,
        private readonly DataSanitizerService $sanitizer,
        private readonly TwitchTokenManager $tokenManager,
    ) {}

    /**
     * Get a single streamer by ID.
     */
    public function getStreamer(string $userId, ?string $accessToken = null): ?StreamerDTO
    {
        try {
            $token = $accessToken ?? $this->tokenManager->getAppAccessToken();
            $data  = $this->apiClient->makeAuthenticatedRequest('users', ['id' => $userId], $token);
            $users = $data['data'] ?? [];

            if (empty($users)) {
                return null;
            }

            return $this->createStreamerDTO($users[0]);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Get current authenticated user.
     */
    public function getCurrentUser(string $accessToken): ?StreamerDTO
    {
        try {
            $data  = $this->apiClient->makeAuthenticatedRequest('users', [], $accessToken);
            $users = $data['data'] ?? [];

            if (empty($users)) {
                return null;
            }

            return $this->createStreamerDTO($users[0]);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Get multiple streamers by IDs.
     *
     * @return StreamerDTO[]
     */
    public function getStreamers(array $userIds, ?string $accessToken = null): array
    {
        try {
            $data  = $this->apiClient->makeAuthenticatedRequest('users', ['id' => $userIds], $accessToken);
            $users = $data['data'] ?? [];

            return array_map([$this, 'createStreamerDTO'], $users);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Create StreamerDTO from API response data.
     */
    private function createStreamerDTO(array $data): StreamerDTO
    {
        return new StreamerDTO(
            id: $data['id'],
            login: $data['login'],
            displayName: $this->sanitizer->sanitizeText($data['display_name']),
            type: $data['type'] ?? '',
            broadcasterType: $data['broadcaster_type'] ?? '',
            description: $this->sanitizer->sanitizeText($data['description'] ?? ''),
            profileImageUrl: $this->sanitizer->sanitizeUrl($data['profile_image_url']),
            offlineImageUrl: $this->sanitizer->sanitizeUrl($data['offline_image_url']),
            viewCount: $this->sanitizer->sanitizeInt($data['view_count'], 0),
            createdAt: $data['created_at'],
            email: $data['email'] ?? null,
        );
    }
}
