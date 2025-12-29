<?php

use App\Models\CutterProfile;
use App\Models\StreamerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create and access streamer and cutter profiles', function () {
    $user = User::factory()->create();

    $streamer = $user->streamerProfile()->create([
        'intro'           => 'Test streamer intro',
        'stream_schedule' => 'MWF',
        'preferred_games' => 'Test Game',
    ]);

    $cutter = $user->cutterProfile()->create([
        'hourly_rate'   => 25.00,
        'response_time' => '24',
        'skills'        => ['editing', 'color-grading'],
        'is_available'  => true,
    ]);

    $user->refresh();

    expect($user->streamerProfile)->toBeInstanceOf(StreamerProfile::class);
    expect($user->cutterProfile)->toBeInstanceOf(CutterProfile::class);

    expect($user->streamerProfile->intro)->toBe('Test streamer intro');
    expect($user->cutterProfile->is_available)->toBeTrue();

    // Explicit PHPUnit assertion to avoid "risky test" when expectations are not counted
    \PHPUnit\Framework\Assert::assertInstanceOf(StreamerProfile::class, $user->streamerProfile);
});
