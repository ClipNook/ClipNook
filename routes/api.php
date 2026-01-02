<?php

declare(strict_types=1);

use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/status', static fn () => response()->json(['status' => 'ok']));

// Health check endpoint (public)
Route::get('/health', HealthCheckController::class);
