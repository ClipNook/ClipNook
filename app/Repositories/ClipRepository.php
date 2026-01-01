<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClipStatus;
use App\Models\Clip;
use App\Models\User;
use App\Repositories\Contracts\ClipRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Clip repository implementation.
 */
class ClipRepository extends BaseRepository implements ClipRepositoryInterface
{
    public function __construct(Clip $model)
    {
        parent::__construct($model);
    }

    /**
     * Escape special characters in search terms for LIKE queries
     */
    protected function escapeSearchTerm(string $term): string
    {
        // Escape % and _ characters that have special meaning in LIKE
        return str_replace(['%', '_'], ['\%', '\_'], $term);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTwitchId(string $twitchClipId): ?Clip
    {
        return $this->model->where('twitch_clip_id', $twitchClipId)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getByStatus(ClipStatus $status): Collection
    {
        return $this->model->where('status', $status->value)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getBySubmitter(User $user): Collection
    {
        return $this->model->where('submitter_id', $user->id)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getByBroadcaster(User $broadcaster): Collection
    {
        return $this->model->where('broadcaster_id', $broadcaster->id)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPending(): Collection
    {
        return $this->model->pending()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getApproved(): Collection
    {
        return $this->model->approved()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getRejected(): Collection
    {
        return $this->model->rejected()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatured(): Collection
    {
        return $this->model->featured()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getForModeration(): Collection
    {
        return $this->model->pending()
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function approveClip(Clip $clip, ?User $moderator = null): bool
    {
        $clip->approve($moderator);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rejectClip(Clip $clip, string $reason, ?User $moderator = null): bool
    {
        $clip->reject($reason, $moderator);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toggleFeatured(Clip $clip): bool
    {
        $clip->toggleFeatured();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getByGame(int $gameId): Collection
    {
        return $this->model->where('game_id', $gameId)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPopular(int $limit = 10): Collection
    {
        return $this->model->approved()
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->approved()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $query): Collection
    {
        $escapedQuery = $this->escapeSearchTerm($query);

        return $this->model->approved()
            ->where(function ($q) use ($escapedQuery) {
                $q->where('title', 'like', "%{$escapedQuery}%")
                    ->orWhere('description', 'like', "%{$escapedQuery}%");
            })
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getStats(): array
    {
        return [
            'total'    => $this->model->count(),
            'pending'  => $this->model->pending()->count(),
            'approved' => $this->model->approved()->count(),
            'rejected' => $this->model->rejected()->count(),
            'featured' => $this->model->featured()->count(),
        ];
    }
}
