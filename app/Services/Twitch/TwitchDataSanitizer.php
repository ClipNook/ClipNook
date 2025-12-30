<?php

namespace App\Services\Twitch;

use App\Helpers\TwitchDataSanitizer as StaticSanitizer;

class TwitchDataSanitizer
{
    /**
     * Sanitize text content from Twitch API to prevent XSS.
     */
    public function sanitizeText(string $text): string
    {
        return StaticSanitizer::sanitizeText($text);
    }

    /**
     * Sanitize URLs from Twitch API.
     */
    public function sanitizeUrl(string $url): string
    {
        return StaticSanitizer::sanitizeUrl($url);
    }

    /**
     * Sanitize integer values with default fallback.
     */
    public function sanitizeInt(mixed $value, int $default = 0): int
    {
        if (! is_numeric($value)) {
            return $default;
        }

        $intValue = (int) $value;

        // Ensure non-negative for counts
        return max(0, $intValue);
    }

    /**
     * Sanitize boolean values.
     */
    public function sanitizeBool(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * Sanitize array of strings.
     */
    public function sanitizeStringArray(array $array): array
    {
        return array_map([$this, 'sanitizeText'], $array);
    }
}
