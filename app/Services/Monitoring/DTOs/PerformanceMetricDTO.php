<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTOs;

readonly class PerformanceMetricDTO
{
    public function __construct(
        public string $name,
        public float $value,
        public array $tags = [],
        public ?string $unit = null,
        public ?int $timestamp = null,
        public ?string $description = null,
    ) {}

    public function getTimestamp(): int
    {
        return $this->timestamp ?? time();
    }

    public function getFormattedValue(): string
    {
        if ($this->unit) {
            return sprintf('%.2f %s', $this->value, $this->unit);
        }

        return (string) $this->value;
    }
}
