<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Twitch Authentication
        'twitch_id',
        'twitch_login',
        'twitch_display_name',
        'twitch_email',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',

        // Avatar Management
        'twitch_avatar',
        'description',
        'custom_avatar_path',
        'avatar_source',
        'avatar_disabled',
        'avatar_disabled_at',

        // Roles and Permissions
        'is_viewer',
        'is_cutter',
        'is_streamer',
        'is_moderator',
        'is_admin',

        // Preferences
        'preferences',
        'scopes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'remember_token',
        'twitch_access_token',
        'twitch_refresh_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        // Timestamps
        'twitch_token_expires_at' => 'datetime',
        'avatar_disabled_at'      => 'datetime',
        'last_activity_at'        => 'datetime',
        'last_login_at'           => 'datetime',

        // Encrypted fields
        'twitch_access_token'     => 'encrypted',
        'twitch_refresh_token'    => 'encrypted',

        // Description
        'description'              => 'string',

        // Booleans
        'avatar_disabled'         => 'boolean',
        'is_viewer'               => 'boolean',
        'is_cutter'               => 'boolean',
        'is_streamer'             => 'boolean',
        'is_moderator'            => 'boolean',
        'is_admin'                => 'boolean',

        // JSON
        'preferences'             => 'array',
        'scopes'                  => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::created(function (self $user) {
            $preferences                 = $user->preferences ?? [];
            $preferences['lang']         = app()->getLocale();
            $user->preferences           = $preferences;
            $user->save();
        });

        static::deleting(function (self $user) {
            $user->deleteAvatar();
        });
    }

    // Relationships

    /**
     * Get the user's sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    // Accessors & Mutators

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->twitch_display_name ?? $this->twitch_login ?? __('user.messages.anonymous_user');
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_disabled) {
            return null;
        }

        if ($this->custom_avatar_path && Storage::exists($this->custom_avatar_path)) {
            return Storage::url($this->custom_avatar_path);
        }

        return $this->twitch_avatar;
    }

    /**
     * Get the user's role badges.
     */
    public function getRoleBadgesAttribute(): array
    {
        $badges = [];

        if ($this->is_admin) {
            $badges[] = __('user.roles.admin');
        }
        if ($this->is_moderator) {
            $badges[] = __('user.roles.moderator');
        }
        if ($this->is_streamer) {
            $badges[] = __('user.roles.streamer');
        }
        if ($this->is_cutter) {
            $badges[] = __('user.roles.cutter');
        }
        if ($this->is_viewer) {
            $badges[] = __('user.roles.viewer');
        }

        return $badges;
    }

    // Helper Methods

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->{'is_'.$role} ?? false;
    }

    /**
     * Check if the user has any of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user is an admin or moderator.
     */
    public function isStaff(): bool
    {
        return $this->is_admin || $this->is_moderator;
    }

    /**
     * Check if the user's Twitch token is expired.
     */
    public function isTwitchTokenExpired(): bool
    {
        return $this->twitch_token_expires_at && $this->twitch_token_expires_at->isPast();
    }

    /**
     * Delete the user's custom avatar files.
     */
    public function deleteAvatar(): void
    {
        if ($this->custom_avatar_path) {
            Storage::delete($this->custom_avatar_path);
        }
    }

    /**
     * Update the user's last activity timestamp.
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Get the user's preferred accent color.
     */
    public function getAccentColorAttribute(): string
    {
        return $this->preferences['accent_color'] ?? 'purple';
    }

    /**
     * Set the user's preferred accent color.
     */
    public function setAccentColorAttribute(string $color): void
    {
        $preferences                 = $this->preferences ?? [];
        $preferences['accent_color'] = $color;
        $this->preferences           = $preferences;
    }
}
