<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

use function array_merge;
use function count;

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
        ?Throwable $previous = null,
    ) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the validation errors.
     */
    final public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are any errors.
     */
    final public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * Get context including validation errors.
     */
    final public function getContext(): array
    {
        return array_merge(parent::getContext(), [
            'errors'      => $this->errors,
            'error_count' => count($this->errors),
        ]);
    }
}
