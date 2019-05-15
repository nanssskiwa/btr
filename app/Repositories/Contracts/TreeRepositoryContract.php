<?php

namespace App\Repositories\Contracts;

interface TreeRepositoryContract extends EloquentRepositoryContract
{
    /**
     * Retrieve all data for tree.
     */
    public function getAll();

    /**
     * Retrieve count.
     */
    public function count();

    /**
     * Find record by value
     * @param $value
     */
    public function findByValue($value);

    /**
     * Retrieve all parents which have a children
     * @return mixed
     */
    public function getAllParentsWhichHaveAChildren();

    /**
     * Retrieve nodes which has not any child
     * @param $id
     * @return mixed
     */
    public function hasNotChild($id);

    /**
     * Retrieve node by child id
     * @param $id
     * @return mixed
     */
    public function findByChildId($id);

    /**
     * Retrieve count parents which have a children
     * @return mixed
     */
    public function getCountAllParentsWhichHaveAChildren();
}
