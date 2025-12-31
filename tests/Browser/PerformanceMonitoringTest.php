<?php

use Laravel\Dusk\Browser;

test('performance monitoring middleware adds headers', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/clips')
            ->assertHeader('X-Response-Time')
            ->assertHeader('X-Memory-Usage')
            ->assertHeader('X-Memory-Peak');

        $responseTime = $browser->driver->executeScript('return performance.getEntriesByType("navigation")[0].responseEnd - performance.getEntriesByType("navigation")[0].requestStart;');
        expect($responseTime)->toBeGreaterThan(0);

        $headers = $browser->driver->executeScript('
            var headers = {};
            var req = new XMLHttpRequest();
            req.open("GET", "/api/clips/recent", false);
            req.send(null);
            var responseHeaders = req.getAllResponseHeaders().split("\n");
            for (var i = 0; i < responseHeaders.length; i++) {
                var header = responseHeaders[i].split(": ");
                if (header[0]) {
                    headers[header[0].toLowerCase()] = header[1];
                }
            }
            return headers;
        ');

        expect($headers)->toHaveKey('x-response-time');
        expect($headers)->toHaveKey('x-memory-usage');
        expect($headers)->toHaveKey('x-memory-peak');

        $responseTimeMs = (float) str_replace('ms', '', $headers['x-response-time']);
        expect($responseTimeMs)->toBeLessThan(5000);
    });
});

test('performance monitoring tracks slow requests', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/api/clips/recent')
            ->assertHeader('X-Response-Time');

        $browser->driver->executeScript('
            var start = Date.now();
            while (Date.now() - start < 100) {}
        ');

        $browser->visit('/api/clips/featured')
            ->assertHeader('X-Response-Time');

        $headers = $browser->driver->executeScript('
            var headers = {};
            var req = new XMLHttpRequest();
            req.open("GET", "/api/clips/featured", false);
            req.send(null);
            var responseHeaders = req.getAllResponseHeaders().split("\n");
            for (var i = 0; i < responseHeaders.length; i++) {
                var header = responseHeaders[i].split(": ");
                if (header[0]) {
                    headers[header[0].toLowerCase()] = header[1];
                }
            }
            return headers;
        ');

        expect($headers)->toHaveKey('x-response-time');
    });
});

test('performance monitoring handles high traffic', function () {
    $this->browse(function (Browser $browser) {
        $responseTimes = [];

        for ($i = 0; $i < 5; $i++) {
            $browser->visit('/api/clips/recent');

            $headers = $browser->driver->executeScript('
                var headers = {};
                var req = new XMLHttpRequest();
                req.open("GET", "/api/clips/recent", false);
                req.send(null);
                var responseHeaders = req.getAllResponseHeaders().split("\n");
                for (var i = 0; i < responseHeaders.length; i++) {
                    var header = responseHeaders[i].split(": ");
                    if (header[0]) {
                        headers[header[0].toLowerCase()] = header[1];
                    }
                }
                return headers;
            ');

            $responseTime    = (float) str_replace('ms', '', $headers['x-response-time']);
            $responseTimes[] = $responseTime;
        }

        expect(count($responseTimes))->toBe(5);
        foreach ($responseTimes as $time) {
            expect($time)->toBeGreaterThan(0);
            expect($time)->toBeLessThan(10000);
        }

        $browser->visit('/api/clips/recent')
            ->assertHeader('X-Memory-Usage')
            ->assertHeader('X-Memory-Peak');
    });
});
