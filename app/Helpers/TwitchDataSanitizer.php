<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\DataSanitizationException;

class TwitchDataSanitizer
{
    /**
     * Sanitize text content from Twitch API to prevent XSS.
     */
    public static function sanitizeText(string $text): string
    {
        // Remove HTML tags
        $text = strip_tags($text);

        // Escape special characters
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // Limit length to prevent abuse
        return \Illuminate\Support\Str::limit($text, config('twitch.security.max_text_length', 1000));
    }

    /**
     * Sanitize URLs from Twitch API.
     */
    public static function sanitizeUrl(string $url): string
    {
        // Validate URL format
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw DataSanitizationException::invalidUrl($url);
        }

        // Only allow HTTPS if required
        if (config('twitch.security.require_https') && ! str_starts_with($url, 'https://')) {
            throw DataSanitizationException::httpsRequired($url);
        }

        // Check against allowed domains
        $allowedDomains = config('twitch.security.allowed_domains', ['twitch.tv', 'static-cdn.jtvnw.net']);
        $host           = parse_url($url, PHP_URL_HOST);

        if (! in_array($host, $allowedDomains)) {
            throw DataSanitizationException::domainNotAllowed($host);
        }

        return $url;
    }

    /**
     * Sanitize image data before download.
     */
    public static function validateImageUrl(string $url): bool
    {
        try {
            self::sanitizeUrl($url);
            // Additional checks for image extensions
            $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

            return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sanitize numeric values.
     */
    public static function sanitizeInt(int $value, int $min = 0, ?int $max = null): int
    {
        $max = $max ?? config('twitch.security.max_view_count', 100000000);

        return max($min, min($max, $value));
    }
}
