<?php

declare(strict_types=1);

namespace App\Services\Image;

use App\Contracts\ImageValidatorInterface;
use App\Enums\ImageMimeType;
use App\Services\Twitch\Exceptions\InvalidImageException;
use InvalidArgumentException;

/**
 * Service for validating images.
 *
 * Provides security-focused validation for image MIME types,
 * URLs, and file sizes to prevent malicious uploads.
 */
final readonly class ImageValidator implements ImageValidatorInterface
{
    /**
     * Maximum allowed image size in bytes (5MB).
     */
    private const int MAX_IMAGE_SIZE = 5_242_880;

    /**
     * Trusted domains for image URLs.
     *
     * @var array<int, string>
     */
    private const array TRUSTED_DOMAINS = [
        'static-cdn.jtvnw.net',
        'clips-media-assets2.twitch.tv',
    ];

    /**
     * Validate image MIME type.
     *
     * @param  string  $imageData  Binary image data
     * @return bool
     *
     * @throws InvalidImageException If MIME type is not allowed
     */
    public function validateMimeType(string $imageData): bool
    {
        $mimeType = $this->getMimeType($imageData);

        if (! ImageMimeType::isAllowed($mimeType)) {
            throw InvalidImageException::invalidMimeType($mimeType);
        }

        return true;
    }

    /**
     * Validate image URL for security.
     *
     * @param  string  $url  URL to validate
     * @return bool
     *
     * @throws InvalidArgumentException If URL is invalid or untrusted
     */
    public function validateUrl(string $url): bool
    {
        $parsed = parse_url($url);

        // Must be HTTPS
        if (($parsed['scheme'] ?? '') !== 'https') {
            throw new InvalidArgumentException('Only HTTPS URLs are allowed');
        }

        // Must be from trusted domains
        $host = $parsed['host'] ?? '';
        foreach (self::TRUSTED_DOMAINS as $domain) {
            if (str_ends_with($host, $domain)) {
                return true;
            }
        }

        throw new InvalidArgumentException('URL is not from a trusted domain');
    }

    /**
     * Validate image file size.
     *
     * @param  string  $imageData  Binary image data
     * @param  int  $maxSize  Maximum size in bytes
     * @return bool
     *
     * @throws InvalidImageException If size exceeds limit
     */
    public function validateSize(string $imageData, int $maxSize = self::MAX_IMAGE_SIZE): bool
    {
        $size = strlen($imageData);

        if ($size > $maxSize) {
            throw InvalidImageException::tooLarge($size, $maxSize);
        }

        return true;
    }

    /**
     * Get MIME type of image data.
     *
     * @param  string  $imageData  Binary image data
     * @return string
     */
    public function getMimeType(string $imageData): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->buffer($imageData);
    }

    /**
     * Get list of allowed MIME types.
     *
     * @return array<int, string>
     */
    public function getAllowedMimeTypes(): array
    {
        return ImageMimeType::values();
    }
}
