<?php

declare(strict_types=1);

namespace App\Enums;

enum VoteType: string
{
    case UPVOTE   = 'upvote';
    case DOWNVOTE = 'downvote';

    public function opposite(): self
    {
        return match ($this) {
            self::UPVOTE   => self::DOWNVOTE,
            self::DOWNVOTE => self::UPVOTE,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::UPVOTE   => 'fa-thumbs-up',
            self::DOWNVOTE => 'fa-thumbs-down',
        };
    }
}
