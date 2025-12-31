<?php

use App\Exceptions\ClipPermissionException;

test('creates exception for submit permission', function () {
    $exception = ClipPermissionException::cannotSubmitForBroadcaster(123);

    expect($exception->getMessage())->toBe('You do not have permission to submit clips for broadcaster 123');
});

test('creates exception for edit permission', function () {
    $exception = ClipPermissionException::cannotEditClip(456);

    expect($exception->getMessage())->toBe('You do not have permission to edit clip 456');
});

test('creates exception for delete permission', function () {
    $exception = ClipPermissionException::cannotDeleteClip(789);

    expect($exception->getMessage())->toBe('You do not have permission to delete clip 789');
});

test('creates exception for moderate permission', function () {
    $exception = ClipPermissionException::cannotModerateClip(101);

    expect($exception->getMessage())->toBe('You do not have permission to moderate clip 101');
});
