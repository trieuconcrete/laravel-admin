<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static \App\Models\Setting set(string $key, mixed $value, string $group = 'general', string $type = 'string', string $description = null)
 * @method static \Illuminate\Support\Collection getByGroup(string $group)
 * @method static array getAllAsArray()
 * @method static void clearCache()
 * @method static void initDefaultSettings()
 *
 * @see \App\Services\SettingService
 */
class Setting extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'setting';
    }
}
