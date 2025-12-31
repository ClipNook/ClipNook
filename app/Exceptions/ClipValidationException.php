<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when clip validation fails with detailed error messages.
 */
class ClipValidationException extends Exception
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed')
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    /**
     * Create exception with validation errors.
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    /**
     * Get the validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are any errors.
     */
    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }
}
