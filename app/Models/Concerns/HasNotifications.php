<?php

namespace App\Models\Concerns;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

trait HasNotifications
{
    /**
     * Get the user's app notifications.
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return Cache::remember(
            "user.{$this->id}.unread_notifications_count",
            now()->addMinutes(5), // Cache for 5 minutes
            fn () => $this->userNotifications()->whereNull('read_at')->count()
        );
    }

    /**
     * Get unread notifications.
     */
    public function unreadAppNotifications()
    {
        return $this->userNotifications()->whereNull('read_at')->get();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): int
    {
        $count = $this->userNotifications()->whereNull('read_at')->update([
            'read_at' => now(),
        ]);

        // Clear cache after marking as read
        $this->clearUnreadNotificationsCountCache();

        return $count;
    }

    /**
     * Create a new notification for the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function createNotification(array $data): Notification
    {
        return $this->userNotifications()->create($data);
    }

    /**
     * Check if user has unread notifications.
     */
    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotificationsCount() > 0;
    }

    /**
     * Get the latest notification.
     */
    public function latestNotification()
    {
        return $this->userNotifications()->latest()->first();
    }

    /**
     * Delete all notifications for the user.
     */
    public function deleteAllNotifications(): int
    {
        return $this->userNotifications()->delete();
    }

    /**
     * Get notifications by type.
     */
    public function notificationsByType(string $type)
    {
        return $this->userNotifications()->where('type', $type)->get();
    }

    /**
     * Clear unread notifications count cache.
     */
    public function clearUnreadNotificationsCountCache(): void
    {
        Cache::forget("user.{$this->id}.unread_notifications_count");
    }
}
