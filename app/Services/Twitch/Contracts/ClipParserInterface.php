<?php

namespace App\Services\Twitch\Contracts;

interface ClipParserInterface
{
    public function parseClipId(string $input): ?string;
}
