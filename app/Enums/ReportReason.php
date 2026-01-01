<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportReason: string
{
    case INAPPROPRIATE = 'inappropriate';
    case SPAM          = 'spam';
    case COPYRIGHT     = 'copyright';
    case MISLEADING    = 'misleading';
    case OTHER         = 'other';

    public function label(): string
    {
        return match ($this) {
            self::INAPPROPRIATE => __('clips.report_reason_inappropriate'),
            self::SPAM          => __('clips.report_reason_spam'),
            self::COPYRIGHT     => __('clips.report_reason_copyright'),
            self::MISLEADING    => __('clips.report_reason_misleading'),
            self::OTHER         => __('clips.report_reason_other'),
        };
    }

    public function severity(): int
    {
        return match ($this) {
            self::COPYRIGHT     => 3,
            self::INAPPROPRIATE => 2,
            self::SPAM          => 2,
            self::MISLEADING    => 1,
            self::OTHER         => 1,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
