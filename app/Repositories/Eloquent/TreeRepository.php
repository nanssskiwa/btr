<?php

namespace App\Repositories\Eloquent;

use App\Models\Tree;
use App\Repositories\Contracts\TreeRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class TreeRepository extends EloquentRepository implements TreeRepositoryContract
{
    /**
     * @param Tree $function
     */
    public function __construct(Tree $function)
    {
        $this->model = $function;
    }

    /**
     * @return Collection|static[]
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * @inheritdoc
     */
    public function findByValue($value)
    {
        return $this->model->where('value', '=', $value)->first();
    }

    /**
     * @return mixed
     */
    public function getAllParentsWhichHaveAChildren()
    {
        return $this->model->where('left_child', '!=', null)->orWhere('right_child', '!=', null)->get();
    }

    /**
     * @return mixed
     */
    public function getCountAllParentsWhichHaveAChildren()
    {
        return $this->model->where('left_child', '!=', null)->orWhere('right_child', '!=', null)->count();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function hasNotChild($id)
    {
        return $this->model->where('left_child', '!=', $id)->where('right_child', '!=', $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findByChildId($id)
    {
        return $this->model->where('left_child', '=', $id)->orWhere('right_child', '=', $id)->first();
    }
}
