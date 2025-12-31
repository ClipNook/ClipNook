<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a Twitch clip cannot be found or accessed.
 */
class ClipNotFoundException extends Exception
{
    /**
     * Create exception for a specific Twitch clip ID.
     */
    public static function forId(string $twitchClipId): self
    {
        return new self("Clip {$twitchClipId} not found on Twitch");
    }

    /**
     * Create exception for an invalid or inaccessible clip URL.
     */
    public static function forUrl(string $url): self
    {
        return new self("Invalid or inaccessible clip URL: {$url}");
    }
}
