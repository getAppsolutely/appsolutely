<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a record by its ID.
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by its ID or throw an exception.
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     *
     * @param array<string, mixed> $data
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * Delete a record.
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Get paginated results.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        array $with = []
    ): LengthAwarePaginator {
        $query = $this->model->query();

        if (!empty($with)) {
            $query->with($with);
        }

        return $this->applyFilters($query, $filters)->paginate($perPage);
    }

    /**
     * Apply filters to the query.
     *
     * @param array<string, mixed> $filters
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Get first record by conditions.
     *
     * @param array<string, mixed> $conditions
     */
    public function firstWhere(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    /**
     * Get records with relationships.
     *
     * @param array<int, string> $relations
     */
    public function with(array $relations): Builder
    {
        return $this->model->with($relations);
    }

    /**
     * Insert multiple records.
     *
     * @param array<int, array<string, mixed>> $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }
}
