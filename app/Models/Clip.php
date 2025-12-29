<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clip model.
 *
 * Represents a Twitch clip with local thumbnail and category assignment.
 *
 * @property int $id
 * @property string $twitch_clip_id
 * @property string $title
 * @property string|null $description
 * @property int|null $category_id
 * @property string $thumbnail_path
 * @property int $broadcaster_id
 * @property int|null $submitted_by_id
 * @property bool $is_public
 * @property int|null $duration
 * @property string|null $creator_name
 * @property string|null $game_id
 * @property string|null $video_id
 * @property \Illuminate\Support\Carbon|null $clip_created_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 */
class Clip extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'twitch_clip_id',
        'title',
        'description',
        'category_id',
        'thumbnail_path',
        'broadcaster_id',
        'submitted_by_id',
        'is_public',
        'duration',
        'creator_name',
        'game_id',
        'video_id',
        'clip_created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'is_public'       => 'boolean',
            'clip_created_at' => 'datetime',
        ];
    }

    /**
     * Get the broadcaster (streamer) for this clip.
     */
    public function broadcaster(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'broadcaster_id');
    }

    /**
     * Get the user who submitted this clip.
     */
    public function submitter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }

    /**
     * Get the category for this clip.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Delete the local thumbnail when the clip is deleted.
     */
    protected static function booted(): void
    {
        parent::boot();
        static::deleting(function (self $clip) {
            if ($clip->thumbnail_path && file_exists(public_path($clip->thumbnail_path))) {
                @unlink(public_path($clip->thumbnail_path));
            }
        });
    }
}
