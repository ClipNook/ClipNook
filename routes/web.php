<?php

use App\Http\Controllers\Auth\TwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::prefix('auth/twitch')->name('auth.twitch.')->group(function () {
    Route::get('/redirect', [TwitchController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [TwitchController::class, 'callback'])->name('callback');
    Route::post('/revoke', [TwitchController::class, 'revoke'])->name('revoke')->middleware('auth');
});

// Login page (DSGVO-compliant info + status) handled by controller
Route::get('/login', [TwitchController::class, 'login'])->name('login')->middleware('guest');

// Local logout (logs out the user and revokes Twitch access if configured)
Route::post('/logout', [TwitchController::class, 'logout'])->name('logout')->middleware('auth');

// Language switcher route (stores locale in session and redirects back)
Route::get('/lang/{locale}', function ($locale) {
    $available = array_keys(config('app.locales', []));
    if (! in_array($locale, $available, true)) {
        return redirect()->back();
    }

    session(['locale' => $locale]);
    cookie()->queue(cookie('locale', $locale, 60 * 24 * 30)); // 30 days

    return redirect()->back();
})->name('lang.switch');
