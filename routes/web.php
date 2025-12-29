<?php

use App\Http\Controllers\Auth\TwitchController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn () => view('home'))->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // User settings
    Route::prefix('settings')->name('settings.')->middleware('throttle:5,1')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');

        // Profile settings
        Route::put('/profile', [App\Http\Controllers\Settings\ProfileController::class, 'update'])->name('profile.update');

        // Preferences
        Route::put('/preferences', [App\Http\Controllers\Settings\PreferencesController::class, 'update'])->name('preferences.update');
        Route::post('/theme', [App\Http\Controllers\Settings\PreferencesController::class, 'updateTheme'])->name('theme.update');

        // Roles
        Route::put('/roles', [App\Http\Controllers\Settings\RolesController::class, 'update'])->name('roles.update');

        // Avatar
        Route::put('/avatar', [App\Http\Controllers\Settings\AvatarController::class, 'update'])->name('avatar.update');
        Route::post('/avatar/upload', [App\Http\Controllers\Settings\AvatarController::class, 'upload'])->name('avatar.upload');

        // Account management
        Route::post('/export', [App\Http\Controllers\Settings\AccountController::class, 'export'])->name('export');
        Route::delete('/account', [App\Http\Controllers\Settings\AccountController::class, 'destroy'])->name('account.destroy');
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
            $user         = $request->user();
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
