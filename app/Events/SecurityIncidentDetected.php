<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Security incident detected event
 */
class SecurityIncidentDetected
{
    use Dispatchable;

    public function __construct(
        public string $type,
        public string $identifier,
        public string $severity,
        public array $details = []
    ) {}
}
