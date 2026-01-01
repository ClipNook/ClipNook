<?php

declare(strict_types=1);

namespace App\Enums;

enum ImageMimeType: string
{
    case JPEG = 'image/jpeg';
    case PNG  = 'image/png';
    case WEBP = 'image/webp';
    case GIF  = 'image/gif';

    /**
     * Get all allowed MIME types as array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }

    /**
     * Check if a MIME type is allowed
     */
    public static function isAllowed(string $mimeType): bool
    {
        return in_array($mimeType, self::values(), true);
    }
}
