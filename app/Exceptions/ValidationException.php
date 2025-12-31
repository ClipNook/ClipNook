<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Base exception for validation errors.
 */
abstract class ValidationException extends AppException
{
    protected array $errors;

    public function __construct(
        array $errors = [],
        string $message = 'Validation failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
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

    /**
     * Get context including validation errors.
     */
    public function getContext(): array
    {
        return array_merge(parent::getContext(), [
            'errors'      => $this->errors,
            'error_count' => count($this->errors),
        ]);
    }
}
