<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutterProfile extends Model
{
    protected $fillable = [
        'user_id',
        'hourly_rate',
        'response_time',
        'skills',
        'is_available',
        'portfolio_url',
        'experience_years',
    ];

    protected $casts = [
        'skills'       => 'array',
        'hourly_rate'  => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
