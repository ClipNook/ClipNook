<?php

declare(strict_types=1);

namespace App\Services\Twitch\Api;

use function filter_var;
use function is_numeric;
use function max;
use function mb_substr;
use function preg_replace;
use function str_starts_with;
use function trim;

use const FILTER_VALIDATE_URL;

/**
 * Service for sanitizing and validating Twitch API data.
 */
final class DataSanitizerService
{
    /**
     * Sanitize text content.
     */
    public function sanitizeText(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        // Remove null bytes and other control characters
        $text = preg_replace('/\x00/', '', $text);

        // Trim whitespace
        $text = trim($text);

        // Limit length to prevent abuse
        return mb_substr($text, 0, 1000);
    }

    /**
     * Sanitize URL.
     */
    public function sanitizeUrl(?string $url): string
    {
        if ($url === null) {
            return '';
        }

        // Basic URL validation
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        // Only allow HTTPS URLs
        if (! str_starts_with($url, 'https://')) {
            return '';
        }

        return $url;
    }

    /**
     * Sanitize integer value with default.
     */
    public function sanitizeInt(mixed $value, int $default = 0): int
    {
        if (! is_numeric($value)) {
            return $default;
        }

        $intValue = (int) $value;

        // Ensure non-negative
        return max(0, $intValue);
    }

    /**
     * Sanitize boolean value.
     */
    public function sanitizeBool(mixed $value): bool
    {
        return (bool) $value;
    }
}
