<?php

declare(strict_types=1);

use App\Exceptions\ClipValidationException;

describe('ClipValidationExceptionTest', static function (): void {
    test('creates exception with errors', static function (): void {
        $errors    = ['title' => ['Title is required'], 'url' => ['URL is invalid']];
        $exception = ClipValidationException::withErrors($errors);
        expect($exception->hasErrors())->toBeTrue();
        expect($exception->errors())->toBe($errors);
    });

    test('checks for empty errors', static function (): void {
        $exception = new ClipValidationException([]);
        expect($exception->hasErrors())->toBeFalse();
    });
});
