<?php

declare(strict_types=1);

namespace App\Services\Twitch\Exceptions;

use Exception;

class InvalidImageException extends Exception
{
    public static function invalidMimeType(string $mimeType): self
    {
        return new self("Invalid image type: {$mimeType}");
    }

    public static function tooLarge(int $size, int $maxSize): self
    {
        return new self("Image too large: {$size} bytes (max: {$maxSize} bytes)");
    }

    public static function invalidUrl(string $url): self
    {
        return new self("Invalid or untrusted image URL: {$url}");
    }

    public static function downloadFailed(int $statusCode): self
    {
        return new self("Failed to download image: HTTP {$statusCode}");
    }
}
