<?php

declare(strict_types=1);

namespace App\Services\Twitch\Enums;

enum TwitchScope: string
{
    case USER_READ_EMAIL            = 'user:read:email';
}
