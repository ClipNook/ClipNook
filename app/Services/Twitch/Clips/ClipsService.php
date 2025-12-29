<?php

declare(strict_types=1);

namespace App\Services\Twitch\Clips;

use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\Contracts\HttpClientInterface;
use App\Services\Twitch\DTOs\ClipData;
use App\Services\Twitch\DTOs\PaginationData;
use App\Services\Twitch\Exceptions\AuthenticationException;
use App\Services\Twitch\Exceptions\RateLimitException;
use App\Services\Twitch\Exceptions\ValidationException;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Service for interacting with Twitch Helix `/clips`, `/games`, and `/videos` endpoints.
 *
 * This class is intentionally lightweight and depends on an `HttpClientInterface` which
 * can be swapped for testing and different transport implementations.
 */
class ClipsService implements ClipsInterface
{
    private readonly string $apiUrl;

    private readonly string $clientId;

    private ?string $accessToken = null;

    /**
     * Per-action rate limit configuration
     *
     * Example shape: [
     *   'get_clips' => ['max' => 60, 'decay' => 60],
     * ]
     *
     * @var array<string, array<string,int>>
     */
    private array $rateLimitActions;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        array $config,
    ) {
        $this->apiUrl   = rtrim($config['api_url'] ?? 'https://api.twitch.tv/helix', '/');
        $this->clientId = $config['client_id'] ?? throw new \InvalidArgumentException('Missing client_id');

        // Allow overriding action-specific limits via configuration. Prefer explicit
        // $config value, otherwise fallback to centralized config('services.twitch.rate_limit.actions')
        $fallback = [
            'get_clips'        => ['max' => 60, 'decay' => 60],
            'get_clips_by_ids' => ['max' => 120, 'decay' => 60],
            'create_clip'      => ['max' => 10, 'decay' => 60],
        ];

        if (isset($config['rate_limit_actions'])) {
            $this->rateLimitActions = $config['rate_limit_actions'];
        } elseif (function_exists('app') && app()->bound('config')) {
            $this->rateLimitActions = config('services.twitch.rate_limit.actions', $fallback);
        } else {
            $this->rateLimitActions = $fallback;
        }
    }

    /**
     * Set access token for authenticated requests.
     *
     * @return $this
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

        // Per-broadcaster rate limiting to avoid abusive polling
        $limit    = $this->getRateLimitSettings('get_clips');
        $limitKey = "twitch:clips:{$broadcasterId}";
        $this->rateLimitOrHit($limitKey, $limit['max'], $limit['decay']);

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
     * Fetch a game by ID from Helix `/games` endpoint
     */
    public function getGameById(string $gameId): ?array
    {
        if (empty($gameId)) {
            return null;
        }

        $cacheKey = "twitch:game:{$gameId}";

        return Cache::remember($cacheKey, 3600, function () use ($gameId) {
            // Rate limit per game id to avoid hot loops
            $limitKey = "twitch:games:{$gameId}";
            $this->rateLimitOrHit($limitKey, 30, 60);

            $response = $this->httpClient->get(
                $this->apiUrl.'/games',
                ['id' => $gameId],
                $this->getHeaders()
            );

            return $response['data'][0] ?? null;
        });
    }

    /**
     * Fetch a video by ID from Helix `/videos` endpoint
     */
    public function getVideoById(string $videoId): ?array
    {
        if (empty($videoId)) {
            return null;
        }

        $cacheKey = "twitch:video:{$videoId}";

        return Cache::remember($cacheKey, 600, function () use ($videoId) {
            // Rate limit per video id to avoid hot loops
            $limitKey = "twitch:videos:{$videoId}";
            $this->rateLimitOrHit($limitKey, 10, 60);

            $response = $this->httpClient->get(
                $this->apiUrl.'/videos',
                ['id' => $videoId],
                $this->getHeaders()
            );

            return $response['data'][0] ?? null;
        });
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

        // Rate limit lookup for batch queries (IDs)
        $limit    = $this->getRateLimitSettings('get_clips_by_ids');
        $limitKey = 'twitch:clips:ids:'.md5(implode(',', $clipIds));
        $this->rateLimitOrHit($limitKey, $limit['max'], $limit['decay']);

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

        // Limit createClip calls per broadcaster to avoid clip spam
        $limit    = $this->getRateLimitSettings('create_clip');
        $limitKey = "twitch:create_clip:{$broadcasterId}";
        $this->rateLimitOrHit($limitKey, $limit['max'], $limit['decay']);

        $response = $this->httpClient->post(
            $this->apiUrl.'/clips',
            $params,
            $this->getHeaders(true)
        );

        if (! isset($response['data'][0])) {
            throw new \RuntimeException('Failed to submit clip');
        }

        return $response['data'][0];
    }

    /**
     * Build request headers.
     *
     * @param  bool  $includeContentType  Whether to include Content-Type header (useful for POST/PUT)
     * @return array<string, string>
     */
    private function getHeaders(bool $includeContentType = false): array
    {
        $headers = [
            'Client-ID' => $this->clientId,
        ];

        // We only add authorization if there is a token set
        if (! empty($this->accessToken)) {
            $headers['Authorization'] = 'Bearer '.$this->accessToken;
        }

        if ($includeContentType) {
            // Only add Content-Type for POSTs
            $headers['Content-Type'] = 'application/json';
        }

        return $headers;
    }

    /**
     * Resolve rate limit settings for a named action
     *
     * @return array{max:int,decay:int}
     */
    private function getRateLimitSettings(string $action): array
    {
        $settings = $this->rateLimitActions[$action] ?? null;

        if (is_array($settings) && isset($settings['max'], $settings['decay'])) {
            return ['max' => (int) $settings['max'], 'decay' => (int) $settings['decay']];
        }

        // Fallback default
        return ['max' => 60, 'decay' => 60];
    }

    /**
     * Simple logical rate limiter helper.
     *
     * @throws RateLimitException
     */
    private function rateLimitOrHit(string $key, int $maxAttempts, int $decaySeconds): void
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);

            throw new RateLimitException('Rate limit for Twitch resource reached', $retryAfter);
        }

        RateLimiter::hit($key, $decaySeconds);
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
