<?php

declare(strict_types=1);

namespace App\Services\Twitch\Clips;

use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\Contracts\HttpClientInterface;
use App\Services\Twitch\DTOs\ClipData;
use App\Services\Twitch\DTOs\PaginationData;
use App\Services\Twitch\Exceptions\AuthenticationException;
use App\Services\Twitch\Exceptions\ValidationException;
use DateTimeInterface;

class ClipsService implements ClipsInterface
{
    private readonly string $apiUrl;

    private readonly string $clientId;

    private ?string $accessToken = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        array $config,
    ) {
        $this->apiUrl   = rtrim($config['api_url'] ?? 'https://api.twitch.tv/helix', '/');
        $this->clientId = $config['client_id'] ?? throw new \InvalidArgumentException('Missing client_id');
    }

    /**
     * Set access token for authenticated requests
     */
    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getClips(
        string $broadcasterId,
        int $limit = 20,
        ?string $after = null,
        ?string $before = null,
        ?DateTimeInterface $startedAt = null,
        ?DateTimeInterface $endedAt = null
    ): PaginationData {
        $this->validateAuth();

        if ($limit < 1 || $limit > 100) {
            throw new ValidationException('Limit must be between 1 and 100');
        }

        $params = [
            'broadcaster_id' => $broadcasterId,
            'first'          => $limit,
        ];

        if ($after !== null) {
            $params['after'] = $after;
        }

        if ($before !== null) {
            $params['before'] = $before;
        }

        if ($startedAt !== null) {
            $params['started_at'] = $startedAt->format('Y-m-d\TH:i:s\Z');
        }

        if ($endedAt !== null) {
            $params['ended_at'] = $endedAt->format('Y-m-d\TH:i:s\Z');
        }

        $response = $this->httpClient->get(
            $this->apiUrl.'/clips',
            $params,
            $this->getHeaders()
        );

        $clips = array_map(
            fn (array $clip) => ClipData::fromArray($clip),
            $response['data'] ?? []
        );

        return new PaginationData(
            data: $clips,
            cursor: $response['pagination']['cursor'] ?? null,
            total: count($clips),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getClipById(string $clipId): ?ClipData
    {
        $clips = $this->getClipsByIds([$clipId]);

        return $clips[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getClipsByIds(array $clipIds): array
    {
        $this->validateAuth();

        if (empty($clipIds)) {
            throw new ValidationException('At least one clip ID is required');
        }

        if (count($clipIds) > 100) {
            throw new ValidationException('Maximum 100 clip IDs allowed');
        }

        $params = ['id' => $clipIds];

        $response = $this->httpClient->get(
            $this->apiUrl.'/clips',
            $params,
            $this->getHeaders()
        );

        return array_map(
            fn (array $clip) => ClipData::fromArray($clip),
            $response['data'] ?? []
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createClip(string $broadcasterId, bool $hasDelay = false): array
    {
        $this->validateAuth();

        if (empty($broadcasterId)) {
            throw new ValidationException('Broadcaster ID is required');
        }

        $params = [
            'broadcaster_id' => $broadcasterId,
            'has_delay'      => $hasDelay ? 'true' : 'false',
        ];

        $response = $this->httpClient->post(
            $this->apiUrl.'/clips',
            $params,
            $this->getHeaders()
        );

        if (! isset($response['data'][0])) {
            throw new \RuntimeException('Failed to submit clip');
        }

        return $response['data'][0];
    }

    /**
     * Get request headers
     *
     * @return array<string, string>
     */
    private function getHeaders(): array
    {
        return [
            'Client-Id'     => $this->clientId,
            'Authorization' => 'Bearer '.$this->accessToken,
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Validate authentication
     *
     * @throws AuthenticationException
     */
    private function validateAuth(): void
    {
        if (empty($this->accessToken)) {
            throw new AuthenticationException('Access token is required. Call setAccessToken() first.');
        }
    }
}
