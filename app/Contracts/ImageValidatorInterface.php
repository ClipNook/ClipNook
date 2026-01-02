<?php

declare(strict_types=1);

namespace App\Contracts;

use InvalidArgumentException;

/**
 * Interface for image validation operations.
 *
 * Provides methods for validating image MIME types, URLs, and file sizes
 * with security-focused validation rules.
 */
interface ImageValidatorInterface
{
    /**
     * Validate an image's MIME type.
     *
     * @param  string $imageData Binary image data
     * @return bool   True if MIME type is allowed
     *
     * @throws \App\Services\Twitch\Exceptions\InvalidImageException If MIME type is invalid
     */
    public function validateMimeType(string $imageData): bool;

    /**
     * Validate an image URL for security.
     *
     * @param  string $url URL to validate
     * @return bool   True if URL is secure and from trusted domain
     *
     * @throws InvalidArgumentException If URL is invalid
     */
    public function validateUrl(string $url): bool;

    /**
     * Validate an image's file size.
     *
     * @param  string $imageData Binary image data
     * @param  int    $maxSize   Maximum size in bytes
     * @return bool   True if size is within limits
     *
     * @throws \App\Services\Twitch\Exceptions\InvalidImageException If size exceeds limit
     */
    public function validateSize(string $imageData, int $maxSize): bool;

    /**
     * Get the MIME type of an image.
     *
     * @param  string $imageData Binary image data
     * @return string MIME type (e.g., 'image/jpeg')
     */
    public function getMimeType(string $imageData): string;

    /**
     * Get allowed MIME types.
     *
     * @return array<int, string> List of allowed MIME types
     */
    public function getAllowedMimeTypes(): array;
}
