<?php

declare(strict_types=1);

namespace App\Services\Twitch\Enums;

enum RequestType: string
{
    case CLIP     = 'clip';
    case GAME     = 'game';
    case STREAMER = 'streamer';
    case VIDEO    = 'video';
}
