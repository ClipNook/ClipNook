<?php

namespace App\Services\Twitch;

use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TwitchGameService
{
    /**
     * Get or create a game from Twitch game ID
     */
    public function getOrCreateGame(string $twitchGameId): Game
    {
        // Check cache first
        $cacheKey = "twitch_game_{$twitchGameId}";
        $game     = Cache::remember($cacheKey, now()->addHours(24), function () use ($twitchGameId) {
            return Game::where('twitch_game_id', $twitchGameId)->first();
        });

        if ($game) {
            return $game;
        }

        // Game not found, fetch from Twitch API
        try {
            $gameData = app(TwitchService::class)->getGame($twitchGameId);

            if (! $gameData) {
                Log::warning('Game not found on Twitch', ['twitch_game_id' => $twitchGameId]);
                throw new \Exception("Game with ID {$twitchGameId} not found on Twitch");
            }

            // Create the game
            $game = Game::create([
                'twitch_game_id'       => $twitchGameId,
                'name'                 => $gameData->name,
                'box_art_url'          => $gameData->boxArtUrl,
                'local_box_art_path'   => null, // Will be set after download
            ]);

            // Dispatch box art download job
            if ($gameData->boxArtUrl) {
                $boxArtPath = 'games/boxart/'.$game->id.'.jpg';
                \App\Jobs\DownloadTwitchImage::dispatch($gameData->boxArtUrl, $boxArtPath, 'thumbnail');

                // Update game with local path
                $game->update(['local_box_art_path' => $boxArtPath]);
            }

            // Cache the new game
            Cache::put($cacheKey, $game, now()->addHours(24));

            Log::info('Game created from Twitch API', [
                'game_id'        => $game->id,
                'twitch_game_id' => $twitchGameId,
                'name'           => $game->name,
            ]);

            return $game;

        } catch (\Exception $e) {
            Log::error('Failed to create game from Twitch API', [
                'twitch_game_id' => $twitchGameId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get game by Twitch ID with caching
     */
    public function getGameByTwitchId(string $twitchGameId): ?Game
    {
        $cacheKey = "twitch_game_{$twitchGameId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($twitchGameId) {
            return Game::where('twitch_game_id', $twitchGameId)->first();
        });
    }

    /**
     * Clear game cache (useful for testing or manual cache invalidation)
     */
    public function clearGameCache(string $twitchGameId): void
    {
        Cache::forget("twitch_game_{$twitchGameId}");
    }
}
