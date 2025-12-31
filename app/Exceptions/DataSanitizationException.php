<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when data sanitization fails.
 */
class DataSanitizationException extends ValidationException
{
    /**
     * Create exception for invalid URL.
     */
    public static function invalidUrl(string $url): self
    {
        return new self(['url' => ['Invalid URL format']], "Invalid URL: {$url}");
    }

    /**
     * Create exception for non-HTTPS URL.
     */
    public static function httpsRequired(string $url): self
    {
        return new self(['url' => ['HTTPS is required']], "HTTPS required for URL: {$url}");
    }

    /**
     * Create exception for disallowed domain.
     */
    public static function domainNotAllowed(string $domain): self
    {
        return new self(['domain' => ['Domain not allowed']], "Domain not allowed: {$domain}");
    }
}

use Exception;

class DataSanitizationException extends Exception {}
