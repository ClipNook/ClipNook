<?php

use App\Http\Controllers\Auth\TwitchController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn () => view('home'))->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // User settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [UserSettingsController::class, 'edit'])->name('edit');
        Route::post('/profile', [UserSettingsController::class, 'updateProfile'])->name('profile.update');
        Route::match(['patch', 'post'], '/preferences', [UserSettingsController::class, 'updatePreferences'])->name('preferences.update');
        Route::post('/roles', [UserSettingsController::class, 'updateRoles'])->name('roles.update');
        Route::post('/avatar', [UserSettingsController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/avatar/upload', [UserSettingsController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::post('/export', [UserSettingsController::class, 'exportData'])->name('export');
        Route::delete('/', [UserSettingsController::class, 'destroy'])->name('destroy');
    });

    // Theme preference
    Route::post('/settings/theme', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'theme' => ['required', 'string', 'in:system,light,dark'],
        ]);
        $user = $request->user();
        $user->theme_preference = $request->theme;
        $user->save();
        return response()->json(['success' => true]);
    });

    // Clips submission (Livewire)
    Route::get('/clips/submit', fn () => view('clips.submit'))->name('clips.submit');

    // Logout
    Route::post('/logout', [TwitchController::class, 'logout'])->name('logout');
});

// Guest-only login
Route::get('/login', [TwitchController::class, 'login'])->name('login')->middleware('guest');

// Twitch OAuth
Route::prefix('auth/twitch')->name('auth.twitch.')->group(function () {
    Route::get('/redirect', [TwitchController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [TwitchController::class, 'callback'])->name('callback');
    Route::post('/revoke', [TwitchController::class, 'revoke'])->name('revoke')->middleware('auth');
});

// Language/Localization
Route::middleware('web')->group(function () {
    // POST: Change language
    Route::post('/lang', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', array_keys(config('app.locales', [])))],
        ]);
        app()->setLocale($request->locale);
        session()->put('locale', $request->locale);
        if ($request->user()) {
            $user = $request->user();
            $user->locale = $request->locale;
            $user->save();
        }
        return response()->json(['success' => true]);
    });
    // GET: Change language (fallback for no-JS)
    Route::get('/lang/{locale}', function ($locale) {
        if (array_key_exists($locale, config('app.locales', []))) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        }
        return redirect()->back();
    });
});
