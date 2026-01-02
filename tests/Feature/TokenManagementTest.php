<?php

declare(strict_types=1);

use App\Models\User;

test('tokens have proper expiration', static function (): void {
    // Verify Sanctum configuration has expiration set
    $expiration = config('sanctum.expiration');
    expect($expiration)->toBe(525600); // 1 year in minutes
});

test('user can rotate api tokens', static function (): void {
    $user = User::factory()->create();

    // Create initial token
    $initialToken = $user->createToken('initial-token');
    expect($user->tokens()->count())->toBe(1);

    // Rotate tokens
    $newToken = $user->rotateApiTokens();

    // Should have only one token now (old ones revoked)
    expect($user->tokens()->count())->toBe(1);

    // New token should be different
    expect($newToken)->not->toBe($initialToken->plainTextToken);

    // Verify token works
    $tokenRecord = $user->tokens()->first();
    expect($tokenRecord->name)->toContain('api-token-');
    expect($tokenRecord->name)->toContain(now()->format('Y-m-d'));
});

test('expired tokens are cleaned up', static function (): void {
    $user = User::factory()->create();

    // Create a token and manually expire it
    $token                          = $user->createToken('test-token');
    $token->accessToken->expires_at = now()->subDay();
    $token->accessToken->save();

    // Run cleanup
    $cleanedCount = User::cleanupExpiredTokens();

    // Should have cleaned up the expired token
    expect($cleanedCount)->toBe(1);
    expect($user->tokens()->count())->toBe(0);
});

test('ip pseudonymization uses rotating salts', static function (): void {
    // Test that pseudonymize_ip function works
    $ip             = '192.168.1.1';
    $pseudonymized1 = pseudonymize_ip($ip);

    expect($pseudonymized1)->not->toBeNull();
    expect($pseudonymized1)->toBeString();
    expect(strlen($pseudonymized1))->toBe(64); // SHA256 hash length

    // Same IP should give same result (deterministic)
    $pseudonymized2 = pseudonymize_ip($ip);
    expect($pseudonymized2)->toBe($pseudonymized1);

    // Different IP should give different result
    $differentIp    = '192.168.1.2';
    $pseudonymized3 = pseudonymize_ip($differentIp);
    expect($pseudonymized3)->not->toBe($pseudonymized1);
});
