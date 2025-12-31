<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'twitch_game_id',
        'name',
        'box_art_url',
        'igdb_id',
    ];

    public function clips(): HasMany
    {
        return $this->hasMany(Clip::class);
    }

    /**
     * Find or create game from Twitch data
     */
    public static function findOrCreateFromTwitch(array $gameData): self
    {
        return static::firstOrCreate(
            ['twitch_game_id' => $gameData['id']],
            [
                'name'        => $gameData['name'],
                'box_art_url' => $gameData['box_art_url'],
                'igdb_id'     => $gameData['igdb_id'] ?? null,
            ]
        );
    }
}
