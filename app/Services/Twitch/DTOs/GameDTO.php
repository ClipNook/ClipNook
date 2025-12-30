<?php

namespace App\Services\Twitch\DTOs;

readonly class GameDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $boxArtUrl,
        public ?string $igdbId,
    ) {}
}
