<?php

use App\Http\Controllers\ClipController;
use App\Http\Controllers\TwitchOAuthController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn () => view('home'))->name('home');

// Twitch OAuth
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', fn () => view('auth.login'))->name('login');
        Route::post('/twitch/login', [TwitchOAuthController::class, 'redirectToTwitch'])->name('twitch.login');
        Route::get('/twitch/callback', [TwitchOAuthController::class, 'handleCallback'])->name('twitch.callback');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/twitch/logout', [TwitchOAuthController::class, 'logout'])->name('twitch.logout');
    });
});

// Clips
Route::group(['prefix' => 'clips', 'as' => 'clips.'], function () {
    Route::get('/', fn () => view('clips.list'))->name('list');
    Route::get('/submit', fn () => view('clips.submit'))->middleware('auth')->name('submit');
    Route::get('/view/{clip}', [ClipController::class, 'view'])->name('view');
});

// Games
Route::group(['prefix' => 'games', 'as' => 'games.'], function () {
    Route::get('/', [App\Http\Controllers\GameController::class, 'index'])->name('list');
    Route::get('/{game}', [App\Http\Controllers\GameController::class, 'show'])->name('view');
});
