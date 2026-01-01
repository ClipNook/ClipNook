<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipReport extends Model
{
    protected $fillable = [
        'clip_id',
        'user_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'reason'      => ReportReason::class,
            'status'      => ReportStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function clip(): BelongsTo
    {
        return $this->belongsTo(Clip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
