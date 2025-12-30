<?php

namespace App\Services\Twitch\Contracts;

use App\Services\Twitch\DTOs\ClipDTO;
use App\Services\Twitch\DTOs\GameDTO;
use App\Services\Twitch\DTOs\StreamerDTO;
use App\Services\Twitch\DTOs\VideoDTO;

interface TwitchApiInterface
{
    public function getClip(string $clipId): ?ClipDTO;

    public function getClips(array $clipIds): array;

    public function getGame(string $gameId): ?GameDTO;

    public function getGames(array $gameIds): array;

    public function getStreamer(string $userId): ?StreamerDTO;

    public function getCurrentUser(): ?StreamerDTO;

    public function getStreamers(array $userIds): array;

    public function getVideo(string $videoId): ?VideoDTO;

    public function getVideos(array $videoIds): array;
}
