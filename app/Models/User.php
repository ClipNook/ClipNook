<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasClipPermissions;
use App\Models\Concerns\HasTwitchIntegration;
use App\Models\Concerns\User\HasAvatar;
use App\Models\Concerns\User\HasRoles;
use App\Models\Concerns\User\HasStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

use function __;
use function app;
use function md5;
use function now;
use function route;
use function substr;
use function uniqid;

/**
 * User Model.
 *
 * Represents a user in the ClipNook platform. Users can be viewers, cutters,
 * streamers, moderators, or administrators with different permission levels.
 *
 * @property int    $id
 * @property string $twitch_id           Unique Twitch user ID
 * @property string $twitch_login        Twitch username
 * @property string $twitch_display_name Display name on Twitch
 * @property string $twitch_email        Email from Twitch
 * @property bool   $is_streamer         Whether user is a streamer
 * @property bool   $is_moderator        Whether user is a moderator
 * @property bool   $is_admin            Whether user is an administrator
 */
final class User extends Authenticatable
{
    use HasApiTokens;
    use HasAvatar;
    use HasClipPermissions;
    use HasFactory;
    use HasRoles;
    use HasStats;
    use HasTwitchIntegration;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Twitch OAuth Data
        'twitch_id',
        'twitch_login',
        'twitch_display_name',
        'twitch_email',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',
        'last_twitch_sync_at',

        // Profile Data
        'description',
        'preferences',
        'scopes',

        // Avatar Management
        'twitch_avatar',
        'custom_avatar_path',
        'avatar_source',

        // Role Flags
        'is_viewer',
        'is_cutter',
        'is_streamer',
        'is_moderator',
        'is_admin',

        // Notification Settings
        'notifications_email',
        'notifications_web',
        'notifications_ntfy',
        'ntfy_server_url',
        'ntfy_topic',
        'ntfy_auth_token',

        // GDPR Compliance
        'deletion_requested_at',
        'data_exported_at',
        'anonymized_at',
        'gdpr_consent_log',
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
        'last_twitch_sync_at'     => 'datetime',
        'last_activity_at'        => 'datetime',
        'last_login_at'           => 'datetime',

        // Encrypted fields
        'twitch_access_token'     => 'encrypted',
        'twitch_refresh_token'    => 'encrypted',
        'ntfy_auth_token'         => 'encrypted',

        // Description
        'description'              => 'string',

        // Booleans
        'is_viewer'               => 'boolean',
        'is_cutter'               => 'boolean',
        'is_streamer'             => 'boolean',
        'is_moderator'            => 'boolean',
        'is_admin'                => 'boolean',
        'notifications_email'     => 'boolean',
        'notifications_web'       => 'boolean',
        'notifications_ntfy'      => 'boolean',

        // JSON
        'preferences'             => 'array',
        'scopes'                  => 'array',
        'gdpr_consent_log'        => 'array',

        // GDPR timestamps
        'deletion_requested_at'   => 'datetime',
        'data_exported_at'        => 'datetime',
        'anonymized_at'           => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        self::created(static function (self $user): void {
            $preferences                 = $user->preferences ?? [];
            $preferences['lang']         = app()->getLocale();
            $user->preferences           = $preferences;
            $user->save();
        });

        self::deleting(static function (self $user): void {
            $user->deleteAvatar();
        });
    }

    /**
     * Clean up expired tokens periodically.
     */
    public static function cleanupExpiredTokens(): int
    {
        return self::query()
            ->whereHas('tokens', static function ($query): void {
                $query->where('expires_at', '<', now());
            })
            ->with('tokens')
            ->get()
            ->each(static function ($user): void {
                $user->tokens()->where('expires_at', '<', now())->delete();
            })
            ->count();
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

    /**
     * Get the user's profile URL.
     */
    public function getProfileUrlAttribute(): string
    {
        return route('users.show', $this->id);
    }

    /**
     * Get the user's Twitch profile URL.
     */
    public function getTwitchProfileUrlAttribute(): string
    {
        return $this->twitch_login ? "https://twitch.tv/{$this->twitch_login}" : '#';
    }

    /**
     * Get the user's join date formatted.
     */
    public function getJoinDateAttribute(): string
    {
        return $this->created_at->format('M j, Y');
    }

    /**
     * Get the user's last activity formatted.
     */
    public function getLastActivityFormattedAttribute(): string
    {
        return $this->last_activity_at?->diffForHumans() ?? __('user.never');
    }

    /**
     * Check if the user has completed their profile.
     */
    public function getProfileCompleteAttribute(): bool
    {
        return ! empty($this->description)
               && ! empty($this->twitch_display_name)
               && ($this->avatar_url !== null);
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
     * Check if the user can moderate content.
     */
    public function canModerate(): bool
    {
        return $this->isStaff() || $this->is_streamer;
    }

    /**
     * Check if the user can submit clips.
     */
    public function canSubmitClips(): bool
    {
        return $this->is_cutter || $this->is_streamer || $this->isStaff();
    }

    /**
     * Check if the user can manage other users.
     */
    public function canManageUsers(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's primary role for display.
     */
    public function getPrimaryRoleAttribute(): string
    {
        if ($this->is_admin) {
            return 'admin';
        }
        if ($this->is_moderator) {
            return 'moderator';
        }
        if ($this->is_streamer) {
            return 'streamer';
        }
        if ($this->is_cutter) {
            return 'cutter';
        }

        return 'viewer';
    }

    /**
     * Get the user's role color for UI.
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->primary_role) {
            'admin'     => 'danger',
            'moderator' => 'warning',
            'streamer'  => 'success',
            'cutter'    => 'info',
            default     => 'secondary',
        };
    }

    /**
     * Check if the user is active (has recent activity).
     */
    public function isActive(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->diffInDays(now()) <= 30;
    }

    /**
     * Get the user's activity level.
     */
    public function getActivityLevelAttribute(): string
    {
        if (! $this->last_activity_at) {
            return 'inactive';
        }

        $days = $this->last_activity_at->diffInDays(now());

        return match (true) {
            $days <= 1   => 'very_active',
            $days <= 7   => 'active',
            $days <= 30  => 'moderately_active',
            default      => 'inactive',
        };
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

    // Relationships

    /**
     * Get all clips submitted by this user.
     * Alias for submittedClips() for backward compatibility.
     */
    public function clips(): HasMany
    {
        return $this->submittedClips();
    }

    /**
     * Get the clips submitted by this user.
     */
    public function submittedClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'submitter_id');
    }

    /**
     * Get the clips from this broadcaster.
     */
    public function broadcasterClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'broadcaster_id');
    }

    /**
     * Get the clips moderated by this user.
     */
    public function moderatedClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'moderated_by');
    }

    /**
     * Get the user's clip votes.
     */
    public function clipVotes(): HasMany
    {
        return $this->hasMany(ClipVote::class);
    }

    /**
     * Get the user's clip comments.
     */
    public function clipComments(): HasMany
    {
        return $this->hasMany(ClipComment::class);
    }

    /**
     * Get the user's clip reports.
     */
    public function clipReports(): HasMany
    {
        return $this->hasMany(ClipReport::class);
    }

    /**
     * Get the user's approved clips.
     */
    public function approvedClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'submitter_id')->approved();
    }

    /**
     * Get the user's pending clips.
     */
    public function pendingClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'submitter_id')->pending();
    }

    /**
     * Get the user's rejected clips.
     */
    public function rejectedClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'submitter_id')->rejected();
    }

    /**
     * Get the user's featured clips.
     */
    public function featuredClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'submitter_id')->featured();
    }

    /**
     * Get the broadcaster settings for this user.
     */
    public function broadcasterSettings(): HasOne
    {
        return $this->hasOne(BroadcasterSettings::class, 'broadcaster_id');
    }

    /**
     * Get the clip permissions given by this broadcaster to other users.
     */
    public function clipPermissionsGiven(): HasMany
    {
        return $this->hasMany(BroadcasterClipPermission::class, 'broadcaster_id');
    }

    /**
     * Get the clip permissions received by this user from broadcasters.
     */
    public function clipPermissionsReceived(): HasMany
    {
        return $this->hasMany(BroadcasterClipPermission::class, 'user_id');
    }

    /**
     * Get the user's consent records.
     */
    public function consents(): HasMany
    {
        return $this->hasMany(UserConsent::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes

    /**
     * Scope for active users.
     */
    public function scopeActive(mixed $query)
    {
        return $query->where('last_activity_at', '>=', now()->subDays(30));
    }

    /**
     * Scope for staff members.
     */
    public function scopeStaff(mixed $query)
    {
        return $query->where(static function ($q): void {
            $q->where('is_admin', true)
                ->orWhere('is_moderator', true);
        });
    }

    /**
     * Scope for streamers.
     */
    public function scopeStreamers(mixed $query)
    {
        return $query->where('is_streamer', true);
    }

    /**
     * Scope for cutters.
     */
    public function scopeCutters(mixed $query)
    {
        return $query->where('is_cutter', true);
    }

    /**
     * Scope for users with expired Twitch tokens.
     */
    public function scopeExpiredTokens(mixed $query)
    {
        return $query->where('twitch_token_expires_at', '<', now());
    }

    /**
     * Check if this broadcaster allows public clip submissions.
     */
    public function allowsPublicClipSubmissions(): bool
    {
        return $this->broadcasterSettings?->allow_public_clip_submissions ?? false;
    }

    /**
     * Check if this user can submit clips for the given broadcaster.
     */
    public function canSubmitClipsFor(self $broadcaster): bool
    {
        // Broadcaster can always submit their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if broadcaster allows public submissions
        if ($broadcaster->allowsPublicClipSubmissions()) {
            return true;
        }

        // Check if user has specific permission
        return $broadcaster->clipPermissionsGiven()
            ->where('user_id', $this->id)
            ->where('can_submit_clips', true)
            ->exists();
    }

    /**
     * Check if this user can edit clips for the given broadcaster.
     */
    public function canEditClipsFor(self $broadcaster): bool
    {
        // Broadcaster can always edit their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission
        return $broadcaster->clipPermissionsGiven()
            ->where('user_id', $this->id)
            ->where('can_edit_clips', true)
            ->exists();
    }

    /**
     * Check if this user can delete clips for the given broadcaster.
     */
    public function canDeleteClipsFor(self $broadcaster): bool
    {
        // Broadcaster can always delete their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission
        return $broadcaster->clipPermissionsGiven()
            ->where('user_id', $this->id)
            ->where('can_delete_clips', true)
            ->exists();
    }

    /**
     * Check if this user can moderate clips for the given broadcaster.
     */
    public function canModerateClipsFor(self $broadcaster): bool
    {
        // Broadcaster can always moderate their own clips
        if ($this->id === $broadcaster->id) {
            return true;
        }

        // Check if user has specific permission
        return $broadcaster->clipPermissionsGiven()
            ->where('user_id', $this->id)
            ->where('can_moderate_clips', true)
            ->exists();
    }

    /**
     * Grant clip submission permission to a user.
     */
    public function grantClipSubmissionPermission(self $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_submit_clips' => true]
        );
    }

    /**
     * Grant clip editing permission to a user.
     */
    public function grantClipEditingPermission(self $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_edit_clips' => true]
        );
    }

    /**
     * Grant clip deletion permission to a user.
     */
    public function grantClipDeletionPermission(self $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_delete_clips' => true]
        );
    }

    /**
     * Grant clip moderation permission to a user.
     */
    public function grantClipModerationPermission(self $user): void
    {
        $this->clipPermissionsGiven()->updateOrCreate(
            ['user_id' => $user->id],
            ['can_moderate_clips' => true]
        );
    }

    /**
     * Revoke clip submission permission from a user.
     */
    public function revokeClipSubmissionPermission(self $user): void
    {
        $this->clipPermissionsGiven()
            ->where('user_id', $user->id)
            ->update(['can_submit_clips' => false]);
    }

    /**
     * Revoke clip editing permission from a user.
     */
    public function revokeClipEditingPermission(self $user): void
    {
        $this->clipPermissionsGiven()
            ->where('user_id', $user->id)
            ->update(['can_edit_clips' => false]);
    }

    /**
     * Revoke clip deletion permission from a user.
     */
    public function revokeClipDeletionPermission(self $user): void
    {
        $this->clipPermissionsGiven()
            ->where('user_id', $user->id)
            ->update(['can_delete_clips' => false]);
    }

    /**
     * Revoke clip moderation permission from a user.
     */
    public function revokeClipModerationPermission(self $user): void
    {
        $this->clipPermissionsGiven()
            ->where('user_id', $user->id)
            ->update(['can_moderate_clips' => false]);
    }

    /**
     * Rotate API tokens for enhanced security.
     * Revokes old tokens and creates a new one with the same abilities.
     */
    public function rotateApiTokens(): string
    {
        // Revoke all existing tokens
        $this->tokens()->delete();

        // Create a new token with a secure name
        $tokenName = 'api-token-'.now()->format('Y-m-d-H-i-s').'-'.substr(md5(uniqid()), 0, 8);

        return $this->createToken($tokenName)->plainTextToken;
    }
}
