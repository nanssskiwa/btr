<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface EloquentRepositoryContract
{
    /**
     * Retrieve all resources.
     *
     * @return EloquentCollection|Model[]
     */
    public function all(): EloquentCollection;

    /**
     * Find a resource by its' id.
     *
     * @param int|string $id
     * @return Model
     */
    public function find($id);

    /**
     * Create a resource.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a resource.
     *
     * @param Model $model
     * @param array $data
     * @return $this
     */
    public function update(Model $model, array $data);

    /**
     * Delete a resource.
     *
     * @param Model $model
     * @return $this
     * @throws \Exception
     */
    public function delete(Model $model);
}
