<?php

declare(strict_types=1);

use App\Models\Clip;
use App\Models\User;
use App\Services\ClipService;

beforeEach(function () {
    // Set required Twitch config for tests
    config(['twitch.client_id' => 'test_client_id']);
    config(['twitch.client_secret' => 'test_client_secret']);
});

test('search sanitizes input and prevents sql injection', function () {
    // Create test data
    $user = User::factory()->create();
    Clip::factory()->create([
        'title'        => 'Test Clip Title',
        'submitter_id' => $user->id,
        'status'       => 'approved',
    ]);

    $clipService = app(ClipService::class);

    // Test normal search
    $results = $clipService->searchClips('Test');
    expect($results)->toHaveCount(1);

    // Test search with special characters (should be sanitized)
    $results = $clipService->searchClips('Test; DROP TABLE clips; --');
    expect($results)->toHaveCount(0); // Should return empty due to sanitization

    // Test search with SQL injection attempt
    $results = $clipService->searchClips("' OR '1'='1");
    expect($results)->toHaveCount(0); // Should be sanitized

    // Test empty/whitespace search
    $results = $clipService->searchClips('   ');
    expect($results)->toHaveCount(0);

    // Test very short search
    $results = $clipService->searchClips('a');
    expect($results)->toHaveCount(0);
});

test('search uses optimized query with eager loading', function () {
    $user = User::factory()->create();
    Clip::factory()->create([
        'title'        => 'Performance Test Clip',
        'submitter_id' => $user->id,
        'status'       => 'approved',
    ]);

    $clipService = app(ClipService::class);

    // This should use the optimized withRelations() scope
    $results = $clipService->searchClips('Performance');

    expect($results)->toHaveCount(1);

    // Verify the first result has loaded relationships
    $clip = $results->first();
    expect($clip->relationLoaded('submitter'))->toBeTrue();
    expect($clip->relationLoaded('broadcaster'))->toBeTrue();
    expect($clip->relationLoaded('game'))->toBeTrue();
    expect($clip->relationLoaded('moderator'))->toBeTrue();
});
