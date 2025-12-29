<?php

use App\Services\Twitch\Exceptions\RateLimitException;
use App\Services\Twitch\Exceptions\TwitchException;
use App\Services\Twitch\Http\CurlHttpClient;

uses(Tests\TestCase::class);

it('retries on 429 then succeeds', function () {
    // Create a test double that simulates a 429 followed by 200
    $calls = 0;

    $client = new class(true, 800, 60, 3, 10) extends CurlHttpClient
    {
        private $calls = 0;

        protected function executeCurlWithOptions(array $options): array
        {
            $this->calls++;

            if ($this->calls === 1) {
                $header = "HTTP/1.1 429 Too Many Requests\r\nRetry-After: 0\r\n\r\n";
                $body   = json_encode(['message' => 'rate limited']);

                return [$header.$body, '', 0, 429, strlen($header)];
            }

            $header = "HTTP/1.1 200 OK\r\n\r\n";
            $body   = json_encode(['data' => []]);

            return [$header.$body, '', 0, 200, strlen($header)];
        }

        protected function delay(int $ms): void
        {
            // no-op for tests
        }
    };

    $res = $client->get('https://api.twitch.tv/helix/clips');

    expect($res)->toBeArray();
});

it('retries on 5xx then succeeds', function () {
    $client = new class(true, 800, 60, 3, 10) extends CurlHttpClient
    {
        private $calls = 0;

        protected function executeCurlWithOptions(array $options): array
        {
            $this->calls++;

            if ($this->calls < 3) {
                $header = "HTTP/1.1 502 Bad Gateway\r\n\r\n";
                $body   = json_encode(['message' => 'bad gateway']);

                return [$header.$body, '', 0, 502, strlen($header)];
            }

            $header = "HTTP/1.1 200 OK\r\n\r\n";
            $body   = json_encode(['data' => ['ok']]);

            return [$header.$body, '', 0, 200, strlen($header)];
        }

        protected function delay(int $ms): void
        {
            // no-op
        }
    };

    $res = $client->get('https://api.twitch.tv/helix/games', ['id' => 'x']);

    expect($res)->toBeArray();
});

it('throws RateLimitException after exhausted retries on 429', function () {
    $client = new class(true, 800, 60, 1, 1) extends CurlHttpClient
    {
        protected function executeCurlWithOptions(array $options): array
        {
            $header = "HTTP/1.1 429 Too Many Requests\r\nRetry-After: 0\r\n\r\n";
            $body   = json_encode(['message' => 'rate limited']);

            return [$header.$body, '', 0, 429, strlen($header)];
        }

        protected function delay(int $ms): void
        {
            // no-op
        }
    };

    $this->expectException(RateLimitException::class);

    $client->get('https://api.twitch.tv/helix/clips');
});

it('throws TwitchException on persistent network error', function () {
    $client = new class(true, 800, 60, 1, 1) extends CurlHttpClient
    {
        protected function executeCurlWithOptions(array $options): array
        {
            // Simulate cURL error
            return [false, 'Could not connect', 7, 0, 0];
        }

        protected function delay(int $ms): void
        {
            // no-op
        }
    };

    $this->expectException(TwitchException::class);

    $client->get('https://api.twitch.tv/helix/clips');
});
