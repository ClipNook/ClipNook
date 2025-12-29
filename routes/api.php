<?php

use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['status' => 'ok']);
});

// routes/api.php
Route::get('/color/{color}', function ($color) {
    $map = [
        'purple' => ['h' => 252, 's' => 83, 'l' => 65],
        'blue'   => ['h' => 221, 's' => 83, 'l' => 65],
        'green'  => ['h' => 142, 's' => 76, 'l' => 45],
        'red'    => ['h' => 0,   's' => 84, 'l' => 53],
        'orange' => ['h' => 25,  's' => 95, 'l' => 47],
        'pink'   => ['h' => 330, 's' => 81, 'l' => 60],
        'indigo' => ['h' => 238, 's' => 75, 'l' => 59],
        'teal'   => ['h' => 173, 's' => 80, 'l' => 36],
        'amber'  => ['h' => 38,  's' => 92, 'l' => 45],
        'slate'  => ['h' => 215, 's' => 13, 'l' => 55],
    ];

    if (! isset($map[$color])) {
        return response()->json(['error' => 'Color not found'], 404);
    }

    $c     = $map[$color];
    $darkL = min($c['l'] + 10, 85);

    $css = <<<CSS
    :root {
        --accent-hue: {$c['h']};
        --accent-saturation: {$c['s']}%;
        --accent-lightness: {$c['l']}%;
        --accent-bg: hsl({$c['h']}, {$c['s']}%, {$c['l']}%);
        --accent-border: hsl({$c['h']}, {$c['s']}%, {$c['l']}%);
        --accent-bgLight: hsl({$c['h']}, 83%, 96%);
        --accent-bg-dark: hsl({$c['h']}, {$c['s']}%, {$darkL}%);
        --accent-border-dark: hsl({$c['h']}, {$c['s']}%, {$darkL}%);
        --accent-bgLight-dark: hsl({$c['h']}, 83%, 15%);
    }
    CSS;

    return response()->json(['css' => $css]);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/settings/accent-color', function (Illuminate\Http\Request $request) {
        $request->validate([
            'accent_color' => ['required', 'string', 'in:purple,blue,green,red,orange,pink,indigo,teal,amber,slate'],
        ]);

        $user               = $request->user();
        $user->accent_color = $request->accent_color;
        $user->save();

        return response()->json(['success' => true]);
    });
});
