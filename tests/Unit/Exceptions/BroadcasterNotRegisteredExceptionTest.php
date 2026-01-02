<?php

declare(strict_types=1);

use App\Exceptions\BroadcasterNotRegisteredException;

describe('BroadcasterNotRegisteredExceptionTest', function (): void {
    test('creates exception for id', function (): void {
        $exception = BroadcasterNotRegisteredException::forId(123);

        expect($exception->getMessage())->toBe('Broadcaster with ID 123 is not registered on this platform');
    });

    test('creates exception for twitch id', function (): void {
        $exception = BroadcasterNotRegisteredException::forTwitchId('TestTwitchId');

        expect($exception->getMessage())->toBe('Broadcaster with Twitch ID TestTwitchId is not registered on this platform');
    });
});
