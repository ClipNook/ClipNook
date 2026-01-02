<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when clip validation fails with detailed error messages.
 */
final class ClipValidationException extends ValidationException
{
    /**
     * Create exception with validation errors.
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }
}
