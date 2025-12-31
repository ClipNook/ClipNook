<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasTwitchIntegration
{
    public function getLastActivityFormattedAttribute(): string
    {
        return $this->last_activity_at?->diffForHumans() ?? __('user.never');
    }

    public function getJoinDateAttribute(): string
    {
        return $this->created_at->format('M j, Y');
    }

    public function getProfileUrlAttribute(): string
    {
        return route('users.show', $this->id);
    }

    public function getTwitchProfileUrlAttribute(): string
    {
        return $this->twitch_login ? "https://twitch.tv/{$this->twitch_login}" : '#';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->twitch_display_name ?? $this->twitch_login ?? __('user.messages.anonymous_user');
    }

    public function isTwitchTokenExpired(): bool
    {
        return $this->twitch_token_expires_at && $this->twitch_token_expires_at->isPast();
    }
}
