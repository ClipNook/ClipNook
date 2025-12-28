<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

interface HttpClientInterface
{
    /**
     * Make a GET request
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, string>  $headers
     * @return array<string, mixed>
     */
    public function get(string $url, array $params = [], array $headers = []): array;

    /**
     * Make a POST request
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, string>  $headers
     * @return array<string, mixed>
     */
    public function post(string $url, array $data = [], array $headers = []): array;

    /**
     * Make a DELETE request
     *
     * @param  array<string, string>  $headers
     * @return array<string, mixed>
     */
    public function delete(string $url, array $headers = []): array;

    /**
     * Set request timeout
     */
    public function setTimeout(int $seconds): self;

    /**
     * Get last response headers
     *
     * @return array<string, string>
     */
    public function getLastResponseHeaders(): array;

    /**
     * Get last HTTP status code
     */
    public function getLastStatusCode(): int;
}
