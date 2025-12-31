<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

readonly class ApiLogEntryDTO
{
    public function __construct(
        public string $endpoint,
        public array $params,
        public ?array $response = null,
        public ?string $error = null,
        public ?string $method = null,
        public ?int $statusCode = null,
        public ?float $duration = null,
    ) {}
}
