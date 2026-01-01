<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\DTOs\GameDTO;
use App\Services\Twitch\Exceptions\TwitchApiException;

/**
 * Service for fetching Twitch game data.
 */
class GameApiService
{
    public function __construct(
        private readonly TwitchApiClientInterface $apiClient,
        private readonly DataSanitizerService $sanitizer,
        private readonly TwitchTokenManager $tokenManager,
    ) {}

    /**
     * Get a single game by ID.
     */
    public function getGame(string $gameId, ?string $accessToken = null): ?GameDTO
    {
        try {
            if ($accessToken) {
                $data = $this->apiClient->makeAuthenticatedRequest('games', ['id' => $gameId], $accessToken);
            } else {
                $token = $this->tokenManager->getAppAccessToken();
                $data  = $this->apiClient->makeRequest('games', ['id' => $gameId], $token);
            }
            $games = $data['data'] ?? [];

            if (empty($games)) {
                return null;
            }

            return $this->createGameDTO($games[0]);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Get multiple games by IDs.
     *
     * @return GameDTO[]
     */
    public function getGames(array $gameIds, ?string $accessToken = null): array
    {
        try {
            $data  = $this->apiClient->makeAuthenticatedRequest('games', ['id' => $gameIds], $accessToken);
            $games = $data['data'] ?? [];

            return array_map([$this, 'createGameDTO'], $games);
        } catch (TwitchApiException $e) {
            throw $e;
        }
    }

    /**
     * Create GameDTO from API response data.
     */
    private function createGameDTO(array $data): GameDTO
    {
        return new GameDTO(
            id: $data['id'],
            name: $this->sanitizer->sanitizeText($data['name']),
            boxArtUrl: $this->sanitizer->sanitizeUrl($data['box_art_url']),
            igdbId: $data['igdb_id'] ?? null,
        );
    }
}
