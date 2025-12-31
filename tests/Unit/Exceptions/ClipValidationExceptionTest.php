<?php

use App\Exceptions\ClipValidationException;

describe('ClipValidationExceptionTest', function () {
    test('creates exception with errors', function () {
        $errors    = ['title' => ['Title is required'], 'url' => ['URL is invalid']];
        $exception = ClipValidationException::withErrors($errors);
        expect($exception->hasErrors())->toBeTrue();
        expect($exception->errors())->toBe($errors);
    });

    test('checks for empty errors', function () {
        $exception = new ClipValidationException([]);
        expect($exception->hasErrors())->toBeFalse();
    });
});
