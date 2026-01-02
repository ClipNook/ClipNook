<?php

declare(strict_types=1);

use App\Models\Clip;
use App\Models\User;

test('clip view route uses uuid for model binding', static function (): void {
    $user        = User::factory()->create();
    $broadcaster = User::factory()->create();

    $clip = Clip::factory()->create([
        'submitter_id'   => $user->id,
        'broadcaster_id' => $broadcaster->id,
        'status'         => 'approved',
    ]);

    // Test that the route generates with UUID
    $url = route('clips.view', $clip);
    expect($url)->toContain($clip->uuid);

    // Test that the route key name is uuid
    expect($clip->getRouteKeyName())->toBe('uuid');
});

test('clip model has uuid route key', static function (): void {
    $clip = new Clip();

    expect($clip->getRouteKeyName())->toBe('uuid');
});
