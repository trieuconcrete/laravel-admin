<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Cast the value attribute based on the type field
     *
     * @param  string  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if (!$this->type) {
            return $value;
        }

        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Set the value attribute based on the type field
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if ($this->type == 'array' || $this->type == 'json') {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->value;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param string $description
     * @return Setting
     */
    public static function set($key, $value, $group = 'general', $type = 'string', $description = null)
    {
        $setting = self::firstOrNew(['key' => $key]);
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
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Get all settings as an associative array
     *
     * @return array
     */
    public static function getAllAsArray()
    {
        $settings = self::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
}
