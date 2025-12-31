<?php

use App\Models\Clip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('clip model works', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $clip = Clip::factory()->create([
        'submitter_id' => $user->id,
        'status'       => 'approved',
    ]);

    expect($clip)->toBeInstanceOf(Clip::class);
    expect($clip->submitter)->toBeInstanceOf(User::class);
    expect($clip->isApproved())->toBeTrue();
});

test('api routes are accessible', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/clips');

    // Debug the response
    if ($response->status() === 500) {
        $content = $response->getContent();
        echo 'Response content: '.$content."\n";
        // Try to decode JSON error
        $data = json_decode($content, true);
        if ($data && isset($data['error'])) {
            echo 'Error: '.$data['error']."\n";
        }
    }

    expect($response->status())->toBe(200);
});
