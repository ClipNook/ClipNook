<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\View\View;

use function compact;
use function view;

final class GameController extends Controller
{
    public function view(Game $game): View
    {
        $game->load(['clips' => static function ($query): void {
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
