<?php

declare(strict_types=1);

namespace App\Services\Twitch\Exceptions;

use Exception;

class TwitchException extends Exception
{
    /**
     * Create exception from API response
     *
     * @param  array<string, mixed>  $response
     */
    public static function fromResponse(array $response, int $statusCode): static
    {
        $message = $response['message'] ?? $response['error'] ?? 'Unknown Twitch API error';

        return new static($message, $statusCode);
    }
}
