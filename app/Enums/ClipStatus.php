<?php

declare(strict_types=1);

namespace App\Enums;

enum ClipStatus: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FLAGGED  = 'flagged';

    public function label(): string
    {
        return match ($this) {
            self::PENDING  => __('clip.status.pending'),
            self::APPROVED => __('clip.status.approved'),
            self::REJECTED => __('clip.status.rejected'),
            self::FLAGGED  => __('clip.status.flagged'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING  => 'secondary',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FLAGGED  => 'warning',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED, self::FLAGGED]);
    }
}
