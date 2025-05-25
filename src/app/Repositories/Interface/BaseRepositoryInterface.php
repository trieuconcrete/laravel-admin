<?php

namespace App\Repositories\Interface;

interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    /**
     * Summary of findBy
     * @param array $params
     * @return TModel|null
     */
    public function findBy(array $params = []);

    /**
     * Summary of updateOrCreate
     * @param array $conditions
     * @param array $data
     * @return TModel
     */
    public function updateOrCreate(array $conditions = [], array $data = []);
}
