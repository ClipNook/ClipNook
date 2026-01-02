<?php

declare(strict_types=1);

use App\Exceptions\ClipValidationException;

describe('ClipValidationExceptionTest', function (): void {
    test('creates exception with errors', function (): void {
        $errors    = ['title' => ['Title is required'], 'url' => ['URL is invalid']];
        $exception = ClipValidationException::withErrors($errors);
        expect($exception->hasErrors())->toBeTrue();
        expect($exception->errors())->toBe($errors);
    });

    test('checks for empty errors', function (): void {
        $exception = new ClipValidationException([]);
        expect($exception->hasErrors())->toBeFalse();
    });
});
