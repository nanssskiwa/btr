<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\EloquentRepositoryContract;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class EloquentRepository implements EloquentRepositoryContract
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @inheritdoc
     */
    public function all(): EloquentCollection
    {
        return $this->model->all();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritdoc
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model)
    {
        $model->delete();

        return $this;
    }
}
