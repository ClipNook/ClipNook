<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'sessions';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_activity' => 'integer',
        'payload'       => 'array',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the session is expired.
     */
    public function isExpired(): bool
    {
        $lifetime = config('session.lifetime', 120); // minutes

        return $this->last_activity + ($lifetime * 60) < time();
    }

    /**
     * Update the last activity timestamp.
     */
    public function touchActivity(): void
    {
        $this->update(['last_activity' => time()]);
    }

    /**
     * Get the session data from payload.
     */
    public function getSessionData(): array
    {
        return unserialize(base64_decode($this->payload)) ?: [];
    }

    /**
     * Set the session data in payload.
     */
    public function setSessionData(array $data): void
    {
        $this->payload = base64_encode(serialize($data));
    }
}
