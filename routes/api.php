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

// Clip management routes
Route::middleware(['auth:sanctum', 'cache.response'])->group(function () {
    Route::apiResource('clips', ClipController::class);
    Route::get('clips/pending/moderation', [ClipController::class, 'pending']);
    Route::get('users/{user}/clips', [ClipController::class, 'userClips']);
});

// GDPR compliance routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('gdpr/data-export', [GDPRController::class, 'exportData']);
    Route::post('gdpr/account/delete-request', [GDPRController::class, 'requestDeletion']);
    Route::delete('gdpr/account/confirm', [GDPRController::class, 'confirmDeletion']);
    Route::get('gdpr/consents', [GDPRController::class, 'getConsents']);
    Route::post('gdpr/consents', [GDPRController::class, 'updateConsents']);
    Route::get('gdpr/retention-info', [GDPRController::class, 'getRetentionInfo']);
});
