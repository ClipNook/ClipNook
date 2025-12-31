<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * User repository implementation.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTwitchId(string $twitchId): ?User
    {
        return $this->model->where('twitch_id', $twitchId)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByTwitchLogin(string $login): ?User
    {
        return $this->model->where('twitch_login', $login)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('twitch_email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamers(): Collection
    {
        return $this->model->where('is_streamer', true)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getModerators(): Collection
    {
        return $this->model->where('is_moderator', true)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdministrators(): Collection
    {
        return $this->model->where('is_admin', true)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUsers(int $days = 30): Collection
    {
        return $this->model
            ->where('last_activity_at', '>=', now()->subDays($days))
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersByRole(string $role): Collection
    {
        return match ($role) {
            'streamer'  => $this->getStreamers(),
            'moderator' => $this->getModerators(),
            'admin'     => $this->getAdministrators(),
            default     => collect(),
        };
    }

    /**
     * {@inheritdoc}
     */
    public function existsByTwitchId(string $twitchId): bool
    {
        return $this->model->where('twitch_id', $twitchId)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function updateLastActivity(User $user): bool
    {
        return $user->update(['last_activity_at' => now()]);
    }

    /**
     * {@inheritdoc}
     */
    public function countByRole(string $role): int
    {
        return match ($role) {
            'streamer'  => $this->model->where('is_streamer', true)->count(),
            'moderator' => $this->model->where('is_moderator', true)->count(),
            'admin'     => $this->model->where('is_admin', true)->count(),
            default     => 0,
        };
    }
}
