<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

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

// AJAX POST route: accepts locale and returns 204 on success or 422 on invalid
Route::post('/lang', function (\Illuminate\Http\Request $request) {
    $available = array_keys(config('app.locales', []));
    // accept locale from JSON or form body
    $locale = (string) ($request->input('locale') ?? $request->json('locale'));

    if (! in_array($locale, $available, true)) {
        return response()->json(['message' => 'Invalid locale'], 422);
    }

    session(['locale' => $locale]);
    cookie()->queue(cookie('locale', $locale, 60 * 24 * 30));

    return response()->noContent();
})->name('lang.switch.post');
