<?php

use App\Exceptions\ClipNotFoundException;

describe('ClipNotFoundExceptionTest', function () {
    test('creates exception for clip id', function () {
        $exception = ClipNotFoundException::forId('TestClip123');

        expect($exception->getMessage())->toBe('Clip TestClip123 not found on Twitch');
    });

    test('creates exception for url', function () {
        $exception = ClipNotFoundException::forUrl('https://clips.twitch.tv/invalid');

        expect($exception->getMessage())->toBe('Invalid or inaccessible clip URL: https://clips.twitch.tv/invalid');
    });
});
