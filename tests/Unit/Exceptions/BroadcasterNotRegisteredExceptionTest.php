<?php

declare(strict_types=1);

use App\Exceptions\BroadcasterNotRegisteredException;

describe('BroadcasterNotRegisteredExceptionTest', function () {
    test('creates exception for id', function () {
        $exception = BroadcasterNotRegisteredException::forId(123);

        expect($exception->getMessage())->toBe('Broadcaster with ID 123 is not registered on this platform');
    });

    test('creates exception for twitch id', function () {
        $exception = BroadcasterNotRegisteredException::forTwitchId('TestTwitchId');

        expect($exception->getMessage())->toBe('Broadcaster with Twitch ID TestTwitchId is not registered on this platform');
    });
});
