<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Base exception for all application-specific exceptions.
 * Provides common functionality and ensures consistent error handling.
 */
abstract class AppException extends Exception
{
    /**
     * Create a new application exception.
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the exception context for logging/debugging.
     */
    public function getContext(): array
    {
        return [
            'line'            => $this->getLine(),
            'file'            => $this->getFile(),
            'code'            => $this->getCode(),
            'message'         => $this->getMessage(),
            'exception_class' => static::class,
        ];
    }
}
