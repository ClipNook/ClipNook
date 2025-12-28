<?php

declare(strict_types=1);

namespace App\Services\Twitch\Exceptions;

class RateLimitException extends TwitchException
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        public readonly int $retryAfter = 60
    ) {
        parent::__construct($message, 429);
    }
}
