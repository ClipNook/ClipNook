<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * User repository interface.
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find user by Twitch ID.
     */
    public function findByTwitchId(string $twitchId): ?User;

    /**
     * Find user by Twitch login.
     */
    public function findByTwitchLogin(string $login): ?User;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get all streamers.
     */
    public function getStreamers(): Collection;

    /**
     * Get all moderators.
     */
    public function getModerators(): Collection;

    /**
     * Get all administrators.
     */
    public function getAdministrators(): Collection;

    /**
     * Get active users (recently active).
     */
    public function getActiveUsers(int $days = 30): Collection;

    /**
     * Get users by role.
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Check if user exists by Twitch ID.
     */
    public function existsByTwitchId(string $twitchId): bool;

    /**
     * Update user's last activity.
     */
    public function updateLastActivity(User $user): bool;

    /**
     * Get user count by role.
     */
    public function countByRole(string $role): int;
}
