<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\GameObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Game extends Model
{
    protected $fillable = [
        'twitch_game_id',
        'name',
        'slug',
        'box_art_url',
        'local_box_art_path',
        'igdb_id',
    ];

    protected static function booted(): void
    {
        self::observe(GameObserver::class);
    }

    /**
     * Find or create game from Twitch data.
     */
    public static function findOrCreateFromTwitch(array $gameData): self
    {
        return self::firstOrCreate(
            ['twitch_game_id' => $gameData['id']],
            [
                'name'        => $gameData['name'],
                'box_art_url' => $gameData['box_art_url'],
                'igdb_id'     => $gameData['igdb_id'] ?? null,
            ]
        );
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function clips(): HasMany
    {
        return $this->hasMany(Clip::class);
    }
}
