<?php

declare(strict_types=1);

namespace App\Models\Concerns\Clip;

use App\Enums\ClipStatus;

/**
 * Handles clip moderation functionality.
 */
trait HasModeration
{
    /**
     * Check if clip is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === ClipStatus::APPROVED;
    }

    /**
     * Check if clip is pending moderation.
     */
    public function isPending(): bool
    {
        return $this->status === ClipStatus::PENDING;
    }

    /**
     * Check if clip is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === ClipStatus::REJECTED;
    }

    /**
     * Check if clip is flagged.
     */
    public function isFlagged(): bool
    {
        return $this->status === ClipStatus::FLAGGED;
    }

    /**
     * Check if clip is active (approved or flagged).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [ClipStatus::APPROVED, ClipStatus::FLAGGED]);
    }

    /**
     * Approve the clip.
     */
    public function approve(\App\Models\User $moderator, ?string $reason = null): bool
    {
        if (! $moderator->is_admin && ! $moderator->is_moderator) {
            return false;
        }

        $this->update([
            'status'            => ClipStatus::APPROVED,
            'moderated_by'      => $moderator->id,
            'moderated_at'      => now(),
            'moderation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Reject the clip.
     */
    public function reject(\App\Models\User $moderator, string $reason): bool
    {
        if (! $moderator->is_admin && ! $moderator->is_moderator) {
            return false;
        }

        $this->update([
            'status'            => ClipStatus::REJECTED,
            'moderated_by'      => $moderator->id,
            'moderated_at'      => now(),
            'moderation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Flag the clip.
     */
    public function flag(\App\Models\User $moderator, string $reason): bool
    {
        if (! $moderator->is_admin && ! $moderator->is_moderator) {
            return false;
        }

        $this->update([
            'status'            => ClipStatus::FLAGGED,
            'moderated_by'      => $moderator->id,
            'moderated_at'      => now(),
            'moderation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            ClipStatus::PENDING  => 'secondary',
            ClipStatus::APPROVED => 'success',
            ClipStatus::REJECTED => 'danger',
            ClipStatus::FLAGGED  => 'warning',
        };
    }

    /**
     * Get status label for UI.
     */
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }
}
