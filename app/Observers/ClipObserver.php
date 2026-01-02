<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Clip;
use App\Services\Cache\QueryCacheService;

final class ClipObserver
{
    public function __construct(
        private QueryCacheService $cache,
    ) {}

    public function creating(Clip $clip): void
    {
        if (empty($clip->uuid)) {
            $clip->uuid = (string) \Illuminate\Support\Str::uuid();
        }
    }

    public function saved(Clip $clip): void
    {
        // Invalidate all clip-related caches
        $this->cache->invalidate(['clips', 'public']);

        // Invalidate user-specific cache
        $this->cache->invalidate("user:{$clip->submitter_id}:clips");
    }

    public function deleted(Clip $clip): void
    {
        $this->cache->invalidate(['clips', 'public']);
    }
}
