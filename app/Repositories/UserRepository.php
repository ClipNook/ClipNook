<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

use function collect;
use function now;

/**
 * User repository implementation.
 */
final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTwitchId(string $twitchId): ?User
    {
        return $this->model->where('twitch_id', $twitchId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByTwitchLogin(string $login): ?User
    {
        return $this->model->where('twitch_login', $login)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('twitch_email', $email)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getStreamers(): Collection
    {
        return $this->model->where('is_streamer', true)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getModerators(): Collection
    {
        return $this->model->where('is_moderator', true)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAdministrators(): Collection
    {
        return $this->model->where('is_admin', true)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveUsers(int $days = 30): Collection
    {
        return $this->model
            ->where('last_activity_at', '>=', now()->subDays($days))
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function existsByTwitchId(string $twitchId): bool
    {
        return $this->model->where('twitch_id', $twitchId)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateLastActivity(User $user): bool
    {
        return $user->update(['last_activity_at' => now()]);
    }

    /**
     * {@inheritDoc}
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
