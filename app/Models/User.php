<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'twitch_id',
        'twitch_login',
        'twitch_display_name',
        'twitch_email',
        'twitch_avatar',
        'avatar_disabled',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',

        // Role flags
        'is_viewer',
        'is_cutter',
        'is_streamer',
        'is_moderator',
        'is_admin',

        // Profile fields
        'intro',
        'available_for_jobs',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
        'twitch_access_token',
        'twitch_refresh_token',
    ];

    /**
     * Attribute casting
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'       => 'datetime',
        'twitch_token_expires_at' => 'datetime',

        // Use Eloquent's encrypted cast for tokens (Laravel encrypted cast)
        'twitch_access_token'     => 'encrypted',
        'twitch_refresh_token'    => 'encrypted',

        // Role flags
        'is_viewer'               => 'boolean',
        'is_cutter'               => 'boolean',
        'is_streamer'             => 'boolean',
        'is_moderator'            => 'boolean',
        'is_admin'                => 'boolean',

        // Avatar preference
        'avatar_disabled'         => 'boolean',

        // Profile fields
        'available_for_jobs'      => 'boolean',
        'intro'                   => 'string',
    ];

    /**
     * Append computed attributes when model is serialized (optional)
     *
     * @var array<int, string>
     */
    protected $appends = [
        'display_name',
        'avatar_url',
    ];

    /**
     * Model boot to cleanup resources when deleting user
     */
    protected static function booted(): void
    {
        static::deleting(function (self $user) {
            $user->deleteAvatar();
        });
    }

    /**
     * Returns the preferred display name (Twitch display name > name > Twitch login).
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->twitch_display_name ?? $this->name ?? $this->twitch_login ?? null;
    }

    /**
     * Returns the user's avatar URL (Twitch, local or fallback SVG).
     */
    public function getAvatarUrlAttribute(): string
    {
        // If the user disabled avatars, always return fallback
        if (! empty($this->avatar_disabled)) {
            return asset('images/avatar-default.svg');
        }

        $avatar = $this->twitch_avatar;

        if (! empty($avatar)) {
            // Check if it's a URL
            if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                return $avatar;
            }

            // Local file in public storage
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($avatar)) {
                return asset('storage/'.$avatar);
            }
        }

        // Fallback to our SVG
        return asset('images/avatar-default.svg');
    }

    /**
     * Checks if the user is connected to Twitch.
     */
    public function isTwitchConnected(): bool
    {
        return ! empty($this->twitch_id);
    }

    /**
     * Whether the user has disabled avatars (opted out of saving)
     */
    public function isAvatarDisabled(): bool
    {
        return (bool) $this->avatar_disabled;
    }

    /**
     * Convenience helpers for role flags
     */
    public function isViewer(): bool
    {
        return (bool) $this->is_viewer;
    }

    public function isCutter(): bool
    {
        return (bool) $this->is_cutter;
    }

    public function isStreamer(): bool
    {
        return (bool) $this->is_streamer;
    }

    public function isModerator(): bool
    {
        return (bool) $this->is_moderator;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Deletes the user's avatar image (local files only).
     */
    public function deleteAvatar(): void
    {
        $avatar = $this->twitch_avatar;

        if (! empty($avatar)) {
            // Only delete if it's not a URL
            if (! filter_var($avatar, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($avatar);
            }
            $this->twitch_avatar = null;
            $this->save();
        }
    }
}
