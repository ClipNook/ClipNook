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
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',
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
     * Return a display name preference (twitch display name > name > twitch login)
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->twitch_display_name ?? $this->name ?? $this->twitch_login ?? null;
    }

    /**
     * Return a public avatar URL (serve from storage/public if local path)
     */
    public function getAvatarUrlAttribute(): string
    {
        $avatar = $this->twitch_avatar;

        if (empty($avatar)) {
            return asset('images/avatar-default.svg');
        }

        // If it's already a remote URL (legacy), return it
        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        // Ensure file exists before returning a URL
        if ($disk->exists($avatar)) {
            return $disk->url($avatar);
        }

        // Fallback to default avatar
        return asset('images/avatar-default.svg');
    }

    /**
     * Whether the user has an associated Twitch account
     */
    public function isTwitchConnected(): bool
    {
        return ! empty($this->twitch_id);
    }

    /**
     * Delete a locally stored avatar (if any) and clear the attribute
     */
    public function deleteAvatar(): bool
    {
        $avatar = $this->twitch_avatar;

        if (empty($avatar)) {
            return false;
        }

        // If remote URL, only clear DB reference
        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            $this->twitch_avatar = null;
            $this->save();

            return true;
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        try {
            if ($disk->exists($avatar)) {
                $disk->delete($avatar);
            }
        } catch (\Throwable $e) {
            // Don't throw on delete failure; log elsewhere if desired
        }

        $this->twitch_avatar = null;
        $this->save();

        return true;
    }
}
