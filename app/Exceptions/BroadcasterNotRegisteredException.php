<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when a broadcaster is not registered on the platform.
 */
class BroadcasterNotRegisteredException extends AuthException
{
    /**
     * Create exception for a Twitch ID that is not registered.
     */
    public static function forTwitchId(string $twitchId): self
    {
        return new self("Broadcaster with Twitch ID {$twitchId} is not registered on this platform");
    }

    /**
     * Create exception for a user ID that is not registered as a broadcaster.
     */
    public static function forId(int $id): self
    {
        return new self("Broadcaster with ID {$id} is not registered on this platform");
    }
}
