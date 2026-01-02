<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function filter_var;
use function hash_hmac;
use function now;
use function substr;

use const FILTER_VALIDATE_IP;

/**
 * Model for managing rotating salts used in IP pseudonymization.
 *
 * This ensures GDPR compliance by regularly rotating the salts used
 * to hash IP addresses, making it harder to correlate user activity
 * over time while maintaining functionality for rate limiting.
 */
final class IpPseudonymizationSalt extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'salt',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from'  => 'datetime',
        'valid_until' => 'datetime',
        'is_active'   => 'boolean',
    ];

    /**
     * Get the currently active salt.
     */
    public static function getActiveSalt(): string
    {
        $activeSalt = self::where('is_active', true)->first();

        if (! $activeSalt) {
            // Create initial salt if none exists
            $activeSalt = self::createInitialSalt();
        }

        return $activeSalt->salt;
    }

    /**
     * Rotate to a new active salt.
     * Deactivates the current salt and creates a new one.
     */
    public static function rotateSalt(): static
    {
        // Deactivate current active salt
        self::where('is_active', true)->update([
            'is_active'   => false,
            'valid_until' => now(),
        ]);

        // Create new active salt
        return self::create([
            'id'         => Str::uuid(),
            'salt'       => Str::random(64), // 512-bit cryptographically secure salt
            'valid_from' => now(),
            'is_active'  => true,
        ]);
    }

    /**
     * Create the initial salt if none exists.
     */
    private static function createInitialSalt(): static
    {
        return static::create([
            'id'         => Str::uuid(),
            'salt'       => Str::random(64),
            'valid_from' => now(),
            'is_active'  => true,
        ]);
    }

    /**
     * Clean up old salts (keep only last 12 months).
     */
    public static function cleanupOldSalts(): int
    {
        return static::where('valid_until', '<', now()->subYear())->delete();
    }

    /**
     * Get pseudonymized IP using the current active salt.
     */
    public static function pseudonymizeIp(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        // Validation
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $salt = static::getActiveSalt();

        // Use stronger hashing method
        $hash = hash_hmac('sha3-256', $ip, $salt);

        // Truncate to 64 characters for database efficiency
        return substr($hash, 0, 64);
    }
}
