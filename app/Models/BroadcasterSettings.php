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
        'clip_submission_permission',
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
        return in_array($this->clip_submission_permission, ['everyone', 'followers', 'subscribers']);
    }

    /**
     * Enable public clip submissions.
     */
    public function enablePublicSubmissions(): void
    {
        $this->update(['clip_submission_permission' => 'everyone']);
    }

    /**
     * Disable public clip submissions.
     */
    public function disablePublicSubmissions(): void
    {
        $this->update(['clip_submission_permission' => 'none']);
    }

    /**
     * Get the clip submission permission.
     */
    public function getClipSubmissionPermission(): string
    {
        return $this->clip_submission_permission ?? 'everyone';
    }

    /**
     * Set the clip submission permission.
     */
    public function setClipSubmissionPermission(string $permission): void
    {
        if (! in_array($permission, ['everyone', 'followers', 'subscribers', 'none'])) {
            throw new \InvalidArgumentException('Invalid clip submission permission');
        }

        $this->update(['clip_submission_permission' => $permission]);
    }

    /**
     * Check if clips can be submitted by everyone.
     */
    public function allowsEveryoneToSubmit(): bool
    {
        return $this->getClipSubmissionPermission() === 'everyone';
    }

    /**
     * Check if clips can be submitted by followers only.
     */
    public function allowsFollowersToSubmit(): bool
    {
        return $this->getClipSubmissionPermission() === 'followers';
    }

    /**
     * Check if clips can be submitted by subscribers only.
     */
    public function allowsSubscribersToSubmit(): bool
    {
        return $this->getClipSubmissionPermission() === 'subscribers';
    }

    /**
     * Check if clip submissions are disabled.
     */
    public function submissionsDisabled(): bool
    {
        return $this->getClipSubmissionPermission() === 'none';
    }
}
