<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(): View
    {
        return view('games.list');
    }

    public function show(Game $game): View
    {
        $game->load(['clips' => function ($query) {
            $query->where('status', 'approved')
                ->latest('created_at')
                ->with(['broadcaster', 'game'])
                ->take(12);
        }]);

        $clipsCount = $game->clips()->where('status', 'approved')->count();

        $streamersCount = $game->clips()
            ->where('status', 'approved')
            ->select('broadcaster_id')
            ->distinct()
            ->count();

        return view('games.view', compact('game', 'clipsCount', 'streamersCount'));
    }
}
