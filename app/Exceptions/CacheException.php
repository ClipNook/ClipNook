<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when cache operations fail.
 */
class CacheException extends Exception
{
    /**
     * Create exception for cache connection failure.
     */
    public static function connectionFailed(string $reason = ''): self
    {
        $message = 'Cache connection failed';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }

    /**
     * Create exception for cache operation failure.
     */
    public static function operationFailed(string $operation, string $reason = ''): self
    {
        $message = "Cache operation '{$operation}' failed";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }

    /**
     * Create exception for cache backend unavailable.
     */
    public static function backendUnavailable(string $backend = 'cache'): self
    {
        return new self("{$backend} backend is unavailable");
    }
}
