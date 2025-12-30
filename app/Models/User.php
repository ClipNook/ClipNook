<?php

namespace App\Models;

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
        'avatar_disabled_at',
        'custom_avatar_path',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',

        // Role flags
        'is_viewer',
        'is_cutter',
        'is_streamer',
        'is_moderator',
        'is_admin',

        // Preferences
        'preferences',
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

        // Preferences
        'preferences'             => 'array',
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
}
