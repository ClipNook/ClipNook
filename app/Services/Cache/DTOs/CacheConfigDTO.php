<?php

declare(strict_types=1);

namespace App\Services\Cache\DTOs;

final readonly class CacheConfigDTO
{
    public function __construct(
        public string $key,
        public int $ttl = 3600,
        public array $tags = [],
        public ?string $prefix = null,
        public bool $useCompression = false,
        public ?string $driver = null,
    ) {}
}
