<?php

namespace App\Services\Twitch;

use App\Models\Game;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TwitchGameService
{
    public function __construct(
        private TwitchApiClient $twitchApiClient
    ) {}

    /**
     * Get game by Twitch ID, create if not exists
     */
    public function getOrCreateGame(string $twitchGameId): ?Game
    {
        // Check if game exists locally
        $game = Game::where('twitch_game_id', $twitchGameId)->first();
        if ($game) {
            return $game;
        }

        // Fetch from Twitch API
        $gameData = $this->twitchApiClient->getGame($twitchGameId);
        if (! $gameData) {
            return null;
        }

        // Create game
        $game = Game::findOrCreateFromTwitch($gameData);

        // Download and store box art (async for performance)
        $this->downloadBoxArtAsync($game);

        return $game;
    }

    /**
     * Download box art asynchronously
     */
    protected function downloadBoxArtAsync(Game $game): void
    {
        // Extract dimensions from URL template
        $boxArtUrl = str_replace(['{width}', '{height}'], ['300', '400'], $game->box_art_url);

        // Download and store
        $imageContent = Http::get($boxArtUrl)->body();

        // Validate image
        if (! $this->isValidImage($imageContent)) {
            return;
        }

        // Store securely
        $filename = 'games/'.$game->twitch_game_id.'.jpg';
        Storage::disk('public')->put($filename, $imageContent);

        // Update game with local path
        $game->update(['box_art_url' => Storage::disk('public')->url($filename)]);
    }

    /**
     * Basic image validation
     */
    protected function isValidImage(string $content): bool
    {
        // Check file size (max 5MB)
        if (strlen($content) > 5 * 1024 * 1024) {
            return false;
        }

        // Check MIME type
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $content);
        finfo_close($finfo);

        return in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp']);
    }
}
