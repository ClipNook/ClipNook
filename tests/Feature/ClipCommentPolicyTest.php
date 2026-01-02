<?php

declare(strict_types=1);

use App\Models\ClipComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows verified users to create comments', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);

    expect($user->can('create', ClipComment::class))->toBeTrue();
});

it('denies unverified users from creating comments', function (): void {
    $user = User::factory()->create(['email_verified_at' => null]);

    expect($user->can('create', ClipComment::class))->toBeTrue();
});

it('allows users to delete their own comments', function (): void {
    $user    = User::factory()->create();
    $comment = ClipComment::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $comment))->toBeTrue();
});

it('denies users from deleting others comments', function (): void {
    $user       = User::factory()->create();
    $otherUser  = User::factory()->create();
    $comment    = ClipComment::factory()->create(['user_id' => $otherUser->id]);

    expect($user->can('delete', $comment))->toBeFalse();
});

it('allows users to update their own comments within 15 minutes', function (): void {
    $user    = User::factory()->create();
    $comment = ClipComment::factory()->create([
        'user_id'    => $user->id,
        'created_at' => now()->subMinutes(10),
    ]);

    expect($user->can('update', $comment))->toBeTrue();
});

it('denies users from updating their own comments after 15 minutes', function (): void {
    $user    = User::factory()->create();
    $comment = ClipComment::factory()->create([
        'user_id'    => $user->id,
        'created_at' => now()->subMinutes(20),
    ]);

    expect($user->can('update', $comment))->toBeFalse();
});

it('allows users to view non-deleted comments', function (): void {
    $user    = User::factory()->create();
    $comment = ClipComment::factory()->create(['is_deleted' => false]);

    expect($user->can('view', $comment))->toBeTrue();
});

it('allows users to view their own deleted comments', function (): void {
    $user    = User::factory()->create();
    $comment = ClipComment::factory()->create([
        'user_id'    => $user->id,
        'is_deleted' => true,
    ]);

    expect($user->can('view', $comment))->toBeTrue();
});

it('denies users from viewing others deleted comments', function (): void {
    $user       = User::factory()->create();
    $otherUser  = User::factory()->create();
    $comment    = ClipComment::factory()->create([
        'user_id'    => $otherUser->id,
        'is_deleted' => true,
    ]);

    expect($user->can('view', $comment))->toBeFalse();
});
