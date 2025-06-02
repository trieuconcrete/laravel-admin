<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingRepository
{
    /**
     * Get all settings
     *
     * @return Collection
     */
    public function all()
    {
        return Setting::all();
    }

    /**
     * Get settings by group
     *
     * @param string $group
     * @return Collection
     */
    public function getByGroup(string $group)
    {
        return Setting::where('group', $group)->get();
    }

    /**
     * Get setting by key
     *
     * @param string $key
     * @return Setting|null
     */
    public function getByKey(string $key)
    {
        return Setting::where('key', $key)->first();
    }

    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $key, $default = null)
    {
        $setting = $this->getByKey($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Create or update a setting
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param string|null $description
     * @return Setting
     */
    public function createOrUpdate(string $key, $value, string $group = 'general', string $type = 'string', ?string $description = null)
    {
        $setting = Setting::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        $setting->type = $type;
        
        if ($description) {
            $setting->description = $description;
        }
        
        $setting->save();
        
        return $setting;
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key)
    {
        return Setting::where('key', $key)->delete();
    }

    /**
     * Get all settings as an associative array
     *
     * @return array
     */
    public function getAllAsArray()
    {
        $settings = $this->all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
}
