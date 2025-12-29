<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'intro',
        'stream_schedule',
        'preferred_games',
        'stream_quality',
        'has_overlay',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
