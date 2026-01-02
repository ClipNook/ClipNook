<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Performance threshold exceeded event.
 */
final class PerformanceThresholdExceeded
{
    use Dispatchable;

    public function __construct(
        public float $threshold,
        public float $value,
        public string $metric,
        public array $context = [],
    ) {}
}
