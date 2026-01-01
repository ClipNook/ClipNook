<?php

namespace App\Http\Controllers;

use App\Models\Clip;
use App\Models\Game;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $limit = config('app.home_items_limit', 6);

        $latestClips = Clip::where('status', 'approved')
            ->select(['id', 'uuid', 'title', 'thumbnail_url', 'duration', 'views', 'upvotes', 'created_at', 'broadcaster_id', 'game_id'])
            ->latest('created_at')
            ->limit($limit)
            ->with([
                'broadcaster:id,twitch_display_name,twitch_login',
                'game:id,name',
            ])
            ->get();

        $topClips = Clip::where('status', 'approved')
            ->select(['id', 'uuid', 'title', 'thumbnail_url', 'duration', 'views', 'upvotes', 'created_at', 'broadcaster_id', 'game_id'])
            ->orderByDesc('upvotes')
            ->limit($limit)
            ->with([
                'broadcaster:id,twitch_display_name,twitch_login',
                'game:id,name',
            ])
            ->get();

        $topGames = Game::select(['id', 'name', 'box_art_url', 'slug'])
            ->withCount(['clips' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('clips_count', 'desc')
            ->limit($limit)
            ->get()
            ->filter(fn ($game) => $game->clips_count > 0);

        return view('home', compact('latestClips', 'topClips', 'topGames'));
    }
}
