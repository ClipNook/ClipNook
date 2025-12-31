<?php

use App\Http\Controllers\ClipController;
use App\Http\Controllers\GDPRController;
use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['status' => 'ok']);
});

// Health check endpoint (public)
Route::get('/health', HealthCheckController::class);
