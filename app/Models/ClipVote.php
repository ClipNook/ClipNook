<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipVote extends Model
{
    protected $fillable = [
        'clip_id',
        'user_id',
        'vote_type',
    ];

    protected function casts(): array
    {
        return [
            'vote_type' => VoteType::class,
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
}
