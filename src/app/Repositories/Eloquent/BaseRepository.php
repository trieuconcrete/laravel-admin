<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

abstract class BaseRepository
{
    protected $model;
    protected $cacheTTL = 60; // Time cache minutes

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $cacheKey = $this->getCacheKey('all');

        return Cache::remember($cacheKey, $this->cacheTTL, function () {
            return $this->model->all();
        });
    }

    public function find($id)
    {
        $cacheKey = $this->getCacheKey("find.$id");

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($id) {
            return $this->model->findOrFail($id);
        });
    }

    public function create(array $data)
    {
        $this->clearCache();
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $this->clearCache();
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $this->clearCache();
        $record = $this->find($id);
        return $record->delete();
    }

    protected function getCacheKey($method)
    {
        return get_class($this->model) . '.' . $method;
    }

    protected function clearCache()
    {
        // Nếu cần clear hết cache liên quan Model này
        Cache::flush();
    }
}
