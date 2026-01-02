<?php

declare(strict_types=1);

namespace App\Models\Concerns\Clip;

use Illuminate\Database\Eloquent\Builder;

trait HasOptimizedQueries
{
    public function scopeWithOptimizedRelations(Builder $query): Builder
    {
        return $query->select([
            'clips.id', 'clips.uuid', 'clips.title', 'clips.description',
            'clips.url', 'clips.thumbnail_url', 'clips.local_thumbnail_path',
            'clips.duration', 'clips.view_count', 'clips.upvotes',
            'clips.downvotes', 'clips.created_at', 'clips.status',
            'clips.submitter_id', 'clips.broadcaster_id', 'clips.game_id',
            'clips.is_featured',
        ])
            ->with([
                'submitter'   => static fn ($q) => $q->select('id', 'twitch_display_name', 'twitch_login'),
                'broadcaster' => static fn ($q) => $q->select('id', 'twitch_display_name', 'twitch_login'),
                'game'        => static fn ($q) => $q->select('id', 'name', 'box_art_url', 'local_box_art_path'),
            ]);
    }

    public function scopeForPublicDisplay(Builder $query): Builder
    {
        return $query->withOptimizedRelations()
            ->approved()
            ->latest('created_at');
    }
}
