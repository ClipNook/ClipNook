<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'consent_version',
        'consented',
        'consented_at',
        'ip_address',
        'user_agent_hash',
    ];

    protected $casts = [
        'consented'    => 'boolean',
        'consented_at' => 'datetime',
    ];

    /**
     * Get the user that owns the consent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the consent is currently valid.
     */
    public function isValid(): bool
    {
        return $this->consented && $this->consented_at;
    }

    /**
     * Get the consent status for a specific type.
     */
    public static function getConsentStatus(int $userId, string $consentType): ?self
    {
        return self::where('user_id', $userId)
            ->where('consent_type', $consentType)
            ->latest()
            ->first();
    }
}
