<?php

declare(strict_types=1);

namespace App\Services\Twitch\Exceptions;

use App\Exceptions\TwitchException;

/**
 * Exception thrown when Twitch API operations fail.
 */
class TwitchApiException extends TwitchException
{
    /**
     * Create exception for API configuration issues.
     */
    public static function invalidConfig(string $details = ''): self
    {
        $message = 'Twitch API configuration is invalid';
        if ($details) {
            $message .= ": {$details}";
        }

        return new self($message);
    }

    /**
     * Create exception for missing refresh token.
     */
    public static function noRefreshToken(): self
    {
        return new self('No refresh token available for token renewal');
    }

    /**
     * Create exception for rate limit exceeded.
     */
    public static function rateLimitExceeded(int $retryAfter = 0): self
    {
        $message = 'Twitch API rate limit exceeded';
        if ($retryAfter > 0) {
            $message .= ". Retry after {$retryAfter} seconds";
        }

        return new self($message);
    }

    /**
     * Create exception for failed thumbnail download.
     */
    public static function thumbnailDownloadFailed(string $reason): self
    {
        return new self("Failed to download thumbnail: {$reason}");
    }

    /**
     * Create exception for failed profile image download.
     */
    public static function profileImageDownloadFailed(string $reason): self
    {
        return new self("Failed to download profile image: {$reason}");
    }

    /**
     * Create exception for token refresh failure.
     */
    public static function tokenRefreshFailed(string $reason = ''): self
    {
        $message = 'Failed to refresh user token';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }

    /**
     * Create exception for app access token fetch failure.
     */
    public static function appTokenFetchFailed(string $reason = ''): self
    {
        $message = 'Failed to fetch app access token';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }

    /**
     * Create exception for API request failure.
     */
    public static function requestFailed(int $statusCode, string $responseBody = ''): self
    {
        $message = "Twitch API request failed with status {$statusCode}";
        if ($responseBody) {
            $message .= ": {$responseBody}";
        }

        return new self($message, $statusCode);
    }

    /**
     * Create exception for authorization code exchange failure.
     */
    public static function codeExchangeFailed(string $reason = ''): self
    {
        $message = 'Failed to exchange authorization code for token';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }
}
