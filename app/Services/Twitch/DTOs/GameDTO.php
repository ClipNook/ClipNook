<?php

declare(strict_types=1);

namespace App\Services\Twitch\DTOs;

final readonly class GameDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $boxArtUrl,
        public ?string $igdbId,
    ) {}
}
