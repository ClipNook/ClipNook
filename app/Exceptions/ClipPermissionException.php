<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when a user lacks permission for clip operations.
 */
final class ClipPermissionException extends AuthException
{
    /**
     * Create exception for insufficient permissions to submit clips for a broadcaster.
     */
    public static function cannotSubmitForBroadcaster(int $broadcasterId): self
    {
        return new self("You do not have permission to submit clips for broadcaster {$broadcasterId}");
    }

    /**
     * Create exception for insufficient permissions to edit a clip.
     */
    public static function cannotEditClip(int $clipId): self
    {
        return new self("You do not have permission to edit clip {$clipId}");
    }

    /**
     * Create exception for insufficient permissions to delete a clip.
     */
    public static function cannotDeleteClip(int $clipId): self
    {
        return new self("You do not have permission to delete clip {$clipId}");
    }

    /**
     * Create exception for insufficient permissions to moderate a clip.
     */
    public static function cannotModerateClip(int $clipId): self
    {
        return new self("You do not have permission to moderate clip {$clipId}");
    }
}
