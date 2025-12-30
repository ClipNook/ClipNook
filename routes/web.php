<?php

use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn () => view('home'))->name('home');
