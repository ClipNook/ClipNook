<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING   = 'pending';
    case REVIEWED  = 'reviewed';
    case RESOLVED  = 'resolved';
    case DISMISSED = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('clips.report_status_pending'),
            self::REVIEWED  => __('clips.report_status_reviewed'),
            self::RESOLVED  => __('clips.report_status_resolved'),
            self::DISMISSED => __('clips.report_status_dismissed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING   => 'yellow',
            self::REVIEWED  => 'blue',
            self::RESOLVED  => 'green',
            self::DISMISSED => 'gray',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
}
