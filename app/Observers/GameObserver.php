<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\DownloadTwitchImage;
use App\Models\Game;
use Illuminate\Support\Str;

use function str_replace;

final class GameObserver
{
    /**
     * Handle the Game "creating" event.
     */
    public function creating(Game $game): void
    {
        $this->generateSlug($game);
    }

    /**
     * Handle the Game "updating" event.
     */
    public function updating(Game $game): void
    {
        if ($game->isDirty('name')) {
            $this->generateSlug($game);
        }
    }

    /**
     * Handle the Game "created" event.
     */
    public function created(Game $game): void
    {
        $this->dispatchBoxArtDownload($game);
    }

    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void {}

    /**
     * Handle the Game "restored" event.
     */
    public function restored(Game $game): void
    {
        $this->dispatchBoxArtDownload($game);
    }

    /**
     * Handle the Game "force deleted" event.
     */
    public function forceDeleted(Game $game): void {}

    /**
     * Generate a unique slug for the game.
     */
    private function generateSlug(Game $game): void
    {
        $baseSlug = Str::slug($game->name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (Game::where('slug', $slug)->where('id', '!=', $game->id ?? 0)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $game->slug = $slug;
    }

    /**
     * Dispatch box art download job for new games.
     */
    private function dispatchBoxArtDownload(Game $game): void
    {
        if ($game->box_art_url && ! $game->local_box_art_path) {
            $boxArtUrl = str_replace(
                ['{width}', '{height}'],
                ['285', '380'],
                $game->box_art_url
            );
            $boxArtPath = 'games/box-art/'.$game->id.'.jpg';
            DownloadTwitchImage::dispatch($boxArtUrl, $boxArtPath, 'box_art', null, $game->id);
        }
    }
}
