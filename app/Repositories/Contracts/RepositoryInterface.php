<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base repository interface providing common CRUD operations.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * Find a model by its primary key.
     */
    public function find(int $id): ?Model;

    /**
     * Find a model by its primary key or throw an exception.
     */
    public function findOrFail(int $id): Model;

    /**
     * Get all models.
     */
    public function all(): Collection;

    /**
     * Create a new model instance.
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing model.
     */
    public function update(Model $model, array $attributes): bool;

    /**
     * Delete a model.
     */
    public function delete(Model $model): bool;

    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find models by specific criteria.
     */
    public function findBy(array $criteria): Collection;

    /**
     * Find first model by specific criteria.
     */
    public function findFirstBy(array $criteria): ?Model;

    /**
     * Count models matching criteria.
     */
    public function count(array $criteria = []): int;

    /**
     * Check if model exists with given criteria.
     */
    public function exists(array $criteria): bool;
}
