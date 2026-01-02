<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClipStatus;
use App\Models\Clip;
use App\Models\User;
use App\Repositories\Contracts\ClipRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

use function config;

/**
 * Clip repository implementation.
 */
final class ClipRepository extends BaseRepository implements ClipRepositoryInterface
{
    public function __construct(Clip $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTwitchId(string $twitchClipId): ?Clip
    {
        return $this->model->where('twitch_clip_id', $twitchClipId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getByStatus(ClipStatus $status): Collection
    {
        return $this->model->where('status', $status->value)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getBySubmitter(User $user): Collection
    {
        return $this->model->where('submitter_id', $user->id)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByBroadcaster(User $broadcaster): Collection
    {
        return $this->model->where('broadcaster_id', $broadcaster->id)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPending(): Collection
    {
        return $this->model->pending()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getApproved(): Collection
    {
        return $this->model->approved()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRejected(): Collection
    {
        return $this->model->rejected()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getFeatured(): Collection
    {
        return $this->model->featured()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getForModeration(): Collection
    {
        return $this->model->pending()
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function approveClip(Clip $clip, ?User $moderator = null): bool
    {
        $clip->approve($moderator);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function rejectClip(Clip $clip, string $reason, ?User $moderator = null): bool
    {
        $clip->reject($reason, $moderator);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function toggleFeatured(Clip $clip): bool
    {
        $clip->toggleFeatured();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getByGame(int $gameId): Collection
    {
        return $this->model->where('game_id', $gameId)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPopular(?int $limit = null): Collection
    {
        $limit ??= config('constants.limits.popular_clips');

        return $this->model->approved()
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRecent(?int $limit = null): Collection
    {
        $limit ??= config('constants.limits.recent_clips');

        return $this->model->approved()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $query): Collection
    {
        return $this->model->approved()
            ->search($query)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getStats(): array
    {
        $stats = $this->model->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN status = ? THEN 1 END) as pending,
                COUNT(CASE WHEN status = ? THEN 1 END) as approved,
                COUNT(CASE WHEN status = ? THEN 1 END) as rejected,
                COUNT(CASE WHEN featured = 1 THEN 1 END) as featured
            ', [
            ClipStatus::PENDING->value,
            ClipStatus::APPROVED->value,
            ClipStatus::REJECTED->value,
        ])
            ->first();

        return [
            'total'    => (int) $stats->total,
            'pending'  => (int) $stats->pending,
            'approved' => (int) $stats->approved,
            'rejected' => (int) $stats->rejected,
            'featured' => (int) $stats->featured,
        ];
    }
}
