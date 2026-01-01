<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model for managing broadcaster-specific settings and preferences.
 *
 * This model controls various settings that broadcasters can configure
 * for their clip management system, such as public submission permissions.
 */
class BroadcasterSettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'broadcaster_id',
        'allow_public_clip_submissions',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'allow_public_clip_submissions' => 'boolean',
    ];

    /**
     * Get the broadcaster that owns these settings.
     */
    public function broadcaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broadcaster_id');
    }

    /**
     * Check if public clip submissions are allowed.
     */
    public function allowsPublicSubmissions(): bool
    {
        return $this->allow_public_clip_submissions;
    }

    /**
     * Enable public clip submissions.
     */
    public function enablePublicSubmissions(): void
    {
        $this->update(['allow_public_clip_submissions' => true]);
    }

    /**
     * Disable public clip submissions.
     */
    public function disablePublicSubmissions(): void
    {
        $this->update(['allow_public_clip_submissions' => false]);
    }
}
