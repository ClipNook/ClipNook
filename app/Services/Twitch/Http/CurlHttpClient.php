<?php

declare(strict_types=1);

namespace App\Services\Twitch\Http;

use App\Services\Twitch\Contracts\HttpClientInterface;
use App\Services\Twitch\Exceptions\RateLimitException;
use App\Services\Twitch\Exceptions\TwitchException;

class CurlHttpClient implements HttpClientInterface
{
    private int $timeout = 30;

    /** @var array<string, string> */
    private array $lastResponseHeaders = [];

    private int $lastStatusCode = 0;

    private int $requestCount = 0;

    private int $requestWindowStart;

    public function __construct(
        private readonly bool $rateLimitEnabled = true,
        private readonly int $maxRequestsPerMinute = 800,
        private readonly int $retryAfter = 60,
    ) {
        $this->requestWindowStart = time();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $url, array $params = [], array $headers = []): array
    {
        if (! empty($params)) {
            $query = $this->buildQuery($params);
            $url .= (str_contains($url, '?') ? '&' : '?').$query;
        }

        return $this->request('GET', $url, [], $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post(string $url, array $data = [], array $headers = []): array
    {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $url, array $headers = []): array
    {
        return $this->request('DELETE', $url, [], $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastResponseHeaders(): array
    {
        return $this->lastResponseHeaders;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastStatusCode(): int
    {
        return $this->lastStatusCode;
    }

    /**
     * Make HTTP request
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, string>  $headers
     * @return array<string, mixed>
     *
     * @throws TwitchException
     * @throws RateLimitException
     */
    private function request(string $method, string $url, array $data = [], array $headers = []): array
    {
        // Rate limiting check (GDPR compliance)
        $this->checkRateLimit();

        $ch = curl_init();

        // Normalize headers and ensure Accept
        $normalized = [];
        $hasAccept  = false;
        foreach ($headers as $key => $value) {
            $normalized[$key] = $value;
            if (strtolower($key) === 'accept') {
                $hasAccept = true;
            }
        }

        if (! $hasAccept) {
            $normalized['Accept'] = 'application/json';
        }

        // Build headers
        $curlHeaders = [];
        foreach ($normalized as $key => $value) {
            $curlHeaders[] = "{$key}: {$value}";
        }

        // Prepare cURL options
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $curlHeaders,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        // Method-specific handling
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;

            // Determine content type
            $contentType = '';
            foreach ($normalized as $k => $v) {
                if (strtolower($k) === 'content-type') {
                    $contentType = strtolower($v);
                    break;
                }
            }

            if ($contentType === 'application/x-www-form-urlencoded') {
                $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            } elseif (str_starts_with($contentType, 'multipart/form-data')) {
                // Let cURL handle multipart arrays
                $options[CURLOPT_POSTFIELDS] = $data;
            } else {
                $options[CURLOPT_POSTFIELDS] = empty($data) ? '' : json_encode($data);
            }
        } elseif ($method === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

        curl_setopt_array($ch, $options);

        // Execute request
        $response   = curl_exec($ch);
        $error      = curl_error($ch);
        $errno      = curl_errno($ch);
        $httpCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        // Handle cURL errors
        if ($errno !== 0 || $response === false) {
            throw new TwitchException("cURL error ({$errno}): {$error}", $errno);
        }

        // Parse response
        $headerString = substr((string) $response, 0, $headerSize);
        $body         = substr((string) $response, $headerSize);

        $this->lastResponseHeaders = $this->parseHeaders($headerString);
        $this->lastStatusCode      = $httpCode;

        // Decode JSON response
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $decoded = ['raw' => $body];
        }

        // Handle HTTP errors
        if ($httpCode >= 400) {
            if ($httpCode === 429) {
                $retryAfter = (int) ($this->lastResponseHeaders['ratelimit-reset'] ?? $this->lastResponseHeaders['retry-after'] ?? $this->retryAfter);
                throw new RateLimitException(retryAfter: $retryAfter);
            }

            throw TwitchException::fromResponse($decoded, $httpCode);
        }

        return $decoded;
    }

    /**
     * Build query string and support repeated params for arrays
     *
     * @param  array<string, mixed>  $params
     */
    private function buildQuery(array $params): string
    {
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $parts[] = urlencode((string) $key).'='.urlencode((string) $v);
                }
            } else {
                $parts[] = urlencode((string) $key).'='.urlencode((string) $value);
            }
        }

        return implode('&', $parts);
    }

    /**
     * Check and enforce rate limiting
     *
     * @throws RateLimitException
     */
    private function checkRateLimit(): void
    {
        if (! $this->rateLimitEnabled) {
            return;
        }

        $now = time();

        // Reset counter if window has passed
        if ($now - $this->requestWindowStart >= 60) {
            $this->requestCount       = 0;
            $this->requestWindowStart = $now;
        }

        // Check if limit exceeded
        if ($this->requestCount >= $this->maxRequestsPerMinute) {
            $waitTime = 60 - ($now - $this->requestWindowStart);
            throw new RateLimitException(retryAfter: max(1, $waitTime));
        }

        $this->requestCount++;
    }

    /**
     * Parse HTTP headers
     *
     * @return array<string, string>
     */
    private function parseHeaders(string $headerString): array
    {
        $headers = [];
        $lines   = explode("\r\n", $headerString);

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $value]                   = explode(':', $line, 2);
                $headers[strtolower(trim($key))] = trim($value);
            }
        }

        return $headers;
    }
}
