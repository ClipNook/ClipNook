<?php

namespace App\Observers;

use App\Models\Game;
use Illuminate\Support\Str;

class GameObserver
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
     * Generate a unique slug for the game
     */
    private function generateSlug(Game $game): void
    {
        $baseSlug = Str::slug($game->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Game::where('slug', $slug)->where('id', '!=', $game->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $game->slug = $slug;
    }

    /**
     * Handle the Game "deleted" event.
     */
    public function deleted(Game $game): void
    {
        //
    }

    /**
     * Handle the Game "restored" event.
     */
    public function restored(Game $game): void
    {
        //
    }

    /**
     * Handle the Game "force deleted" event.
     */
    public function forceDeleted(Game $game): void
    {
        //
    }
}
