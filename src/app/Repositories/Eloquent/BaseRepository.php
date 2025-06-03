<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

abstract class BaseRepository
{
    protected $model;
    protected $cacheTTL = 60; // Time cache minutes
    protected $settingService;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Get the setting service instance
     * 
     * @return \App\Services\SettingService
     */
    protected function getSettingService()
    {
        if (!$this->settingService) {
            $this->settingService = App::make('App\\Services\\SettingService');
        }
        
        return $this->settingService;
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

    /**
     * Summary of findBy
     * @param array $params
     * @return Model|null
     */
    public function findBy(array $params = []) {
        return $this->model->where($params)->first();
    }

    /**
     * Summary of updateOrCreate
     * @param array $conditions
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $conditions = [], array $data = [])
    {
        return $this->model->updateOrCreate($conditions, $data);
    }
    
    /**
     * Get pagination limit from settings
     * 
     * @param int $default
     * @return int
     */
    protected function getPaginationLimit($default = 10)
    {
        return (int) $this->getSettingService()->get('pagination_limit', $default);
    }
}
