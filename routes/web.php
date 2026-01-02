<?php

declare(strict_types=1);

use App\Http\Controllers\ClipController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TwitchOAuthController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Twitch OAuth
Route::group(['prefix' => 'auth', 'as' => 'auth.'], static function (): void {
    Route::middleware('guest')->group(static function (): void {
        Route::get('/login', static fn () => view('auth.login'))->name('login');
        Route::post('/twitch/login', [TwitchOAuthController::class, 'redirectToTwitch'])
            ->middleware('throttle:5,1') // 5 attempts per minute
            ->name('twitch.login');
        Route::get('/twitch/callback', [TwitchOAuthController::class, 'handleCallback'])->name('twitch.callback');
    });

    Route::middleware('auth')->group(static function (): void {
        Route::post('/twitch/logout', [TwitchOAuthController::class, 'logout'])->name('twitch.logout');
    });
});

// Clips
Route::group(['prefix' => 'clips', 'as' => 'clips.'], static function (): void {
    Route::get('/', static fn () => view('clips.list'))->name('list');
    Route::get('/submit', static fn () => view('clips.submit'))->middleware('auth')->name('submit');
    Route::get('/view/{clip}', [ClipController::class, 'view'])->name('view');
});

// Games
Route::group(['prefix' => 'games', 'as' => 'games.'], static function (): void {
    Route::get('/', [GameController::class, 'index'])->name('list');
    Route::get('/{game}', [GameController::class, 'show'])->name('view');
});

// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], static function (): void {
    Route::get('/clips', static fn () => view('admin.clips'))->name('clips');
});

// Theme
Route::post('/theme/{theme}', static function (string $theme) {
    if (in_array($theme, ['violet', 'blue', 'green', 'red'], true)) {
        session(['theme' => $theme]);

        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Invalid theme'], 400);
})->name('theme.set');
