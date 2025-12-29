<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'avatar_disabled_at',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',

        // Avatar fields
        'custom_avatar_path',
        'custom_avatar_thumbnail_path',
        'avatar_source',

        // Role flags
        'is_viewer',
        'is_cutter',
        'is_streamer',
        'is_moderator',
        'is_admin',

        // Profile fields
        'intro',
        'available_for_jobs',
        'allow_clip_sharing',

        // Preferences
        'preferences',
        'accent_color',
        'theme_preference',
        'locale',
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
        'avatar_disabled_at'      => 'datetime',

        // Profile fields
        'available_for_jobs'      => 'boolean',
        'allow_clip_sharing'      => 'boolean',
        'intro'                   => 'string',

        // Preferences
        'preferences'             => 'array',
        'theme_preference'        => 'string',
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
        parent::boot();

        static::deleting(function (self $user) {
            $user->deleteAvatar();
        });
    }

    /**
     * Returns the preferred display name (Twitch display name > Twitch login).
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->twitch_display_name ?? $this->twitch_login ?? null;
    }

    /**
     * Returns the user's avatar URL (Twitch, local or fallback SVG).
     */
    public function getAvatarUrlAttribute(): string
    {
        // If the user disabled avatars, always return fallback
        if ($this->isAvatarDisabled()) {
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

    public function getThemeAttribute()
    {
        return $this->theme_preference ?? 'system';
    }

    /**
     * Checks if the user is connected to Twitch.
     */
    public function isTwitchConnected(): bool
    {
        return ! empty($this->twitch_id);
    }

    /**
     * Whether the user has disabled avatars.
     *
     * Supports legacy boolean flag (avatar_disabled) and timestamp (avatar_disabled_at).
     */
    public function isAvatarDisabled(): bool
    {
        return (bool) $this->avatar_disabled || $this->avatar_disabled_at !== null;
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

    /**
     * Calculate profile completion percentage.
     */
    public function profileCompletion(): int
    {
        $steps = [
            'profile'     => $this->isProfileComplete(),
            'avatar'      => ! $this->isAvatarDisabled(),
            'roles'       => $this->isStreamer() || $this->isCutter(),
            'preferences' => $this->hasPreferencesSet(),
        ];

        $completed = count(array_filter($steps));

        return (int) round(($completed / count($steps)) * 100);
    }

    /**
     * Determine if base profile is complete.
     */
    public function isProfileComplete(): bool
    {
        return ! empty($this->display_name)
            && ! empty($this->email)
            && filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Whether user has set timezone & locale.
     */
    public function hasPreferencesSet(): bool
    {
        return ! empty($this->timezone) && ! empty($this->locale);
    }

    /**
     * List completed profile step keys.
     *
     * @return array<int, string>
     */
    public function completedProfileSteps(): array
    {
        $steps = [];

        if ($this->isProfileComplete()) {
            $steps[] = 'profile';
        }

        if (! $this->isAvatarDisabled()) {
            $steps[] = 'avatar';
        }

        if ($this->isStreamer() || $this->isCutter()) {
            $steps[] = 'roles';
        }

        if ($this->hasPreferencesSet()) {
            $steps[] = 'preferences';
        }

        return $steps;
    }

    /**
     * Human readable profile updated at or 'never'.
     */
    public function profileUpdatedAt(): string
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : __('ui.never');
    }

    /**
     * One-to-one relationship to the StreamerProfile model.
     */
    public function streamerProfile(): HasOne
    {
        return $this->hasOne(StreamerProfile::class);
    }

    /**
     * One-to-one relationship to the CutterProfile model.
     */
    public function cutterProfile(): HasOne
    {
        return $this->hasOne(CutterProfile::class);
    }
}
