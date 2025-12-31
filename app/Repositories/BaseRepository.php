<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Abstract base repository providing common CRUD operations.
 *
 * @template TModel of Model
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model instance.
     */
    protected function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set the model instance.
     */
    protected function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstBy(array $criteria): ?Model
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->count();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(array $criteria): bool
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->exists();
    }
}
