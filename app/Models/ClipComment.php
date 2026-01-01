<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ClipCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClipComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clip_id',
        'user_id',
        'content',
        'parent_id',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function newFactory(): ClipCommentFactory
    {
        return ClipCommentFactory::new();
    }

    public function clip(): BelongsTo
    {
        return $this->belongsTo(Clip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ClipComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ClipComment::class, 'parent_id');
    }
}
