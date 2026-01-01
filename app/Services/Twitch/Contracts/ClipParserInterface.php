<?php

declare(strict_types=1);

namespace App\Services\Twitch\Contracts;

interface ClipParserInterface
{
    public function parseClipId(string $input): ?string;
}
