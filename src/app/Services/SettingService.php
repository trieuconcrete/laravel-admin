<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingService
{
    /**
     * @var SettingRepository
     */
    public $settingRepository;

    /**
     * @var string
     */
    protected $cachePrefix = 'settings_';

    /**
     * @var int
     */
    protected $cacheTtl = 86400; // 24 hours

    /**
     * SettingService constructor.
     *
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Get all settings
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->settingRepository->all();
    }

    /**
     * Get settings by group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public function getByGroup(string $group)
    {
        return Cache::remember($this->cachePrefix . 'group_' . $group, $this->cacheTtl, function () use ($group) {
            return $this->settingRepository->getByGroup($group);
        });
    }

    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember($this->cachePrefix . $key, $this->cacheTtl, function () use ($key, $default) {
            return $this->settingRepository->getValue($key, $default);
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param string|null $description
     * @return \App\Models\Setting
     */
    public function set(string $key, $value, string $group = 'general', string $type = 'string', ?string $description = null)
    {
        // Cập nhật setting trong database
        $setting = $this->settingRepository->createOrUpdate($key, $value, $group, $type, $description);
        
        // Xóa cache
        $this->forgetCache($key);
        $this->forgetCache('group_' . $group);
        
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
        $setting = $this->settingRepository->getByKey($key);
        
        if ($setting) {
            // Xóa cache
            $this->forgetCache($key);
            $this->forgetCache('group_' . $setting->group);
            
            return $this->settingRepository->delete($key);
        }
        
        return false;
    }

    /**
     * Get all settings as an associative array
     *
     * @return array
     */
    public function getAllAsArray()
    {
        return Cache::remember($this->cachePrefix . 'all_array', $this->cacheTtl, function () {
            return $this->settingRepository->getAllAsArray();
        });
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public function clearCache()
    {
        $keys = Cache::get($this->cachePrefix . 'cache_keys', []);
        
        foreach ($keys as $key) {
            Cache::forget($this->cachePrefix . $key);
        }
        
        Cache::forget($this->cachePrefix . 'cache_keys');
    }

    /**
     * Forget a specific cache key
     *
     * @param string $key
     * @return void
     */
    protected function forgetCache(string $key)
    {
        Cache::forget($this->cachePrefix . $key);
        
        // Thêm key vào danh sách các key đã cache
        $keys = Cache::get($this->cachePrefix . 'cache_keys', []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::forever($this->cachePrefix . 'cache_keys', $keys);
        }
    }

    /**
     * Khởi tạo các cài đặt mặc định
     *
     * @return void
     */
    public function initDefaultSettings()
    {
        try {
            // Cài đặt thông tin công ty
            $this->set('company_name', 'Công ty Vận tải HPL', 'company', 'string', 'Tên công ty');
            $this->set('company_address', '123 Đường ABC, Quận XYZ, TP. HCM', 'company', 'string', 'Địa chỉ công ty');
            $this->set('company_phone', '0123456789', 'company', 'string', 'Số điện thoại công ty');
            $this->set('company_email', 'info@vantaihpl.com', 'company', 'string', 'Email công ty');
            $this->set('company_tax_code', '0123456789', 'company', 'string', 'Mã số thuế');
            $this->set('company_logo', 'images/logo.png', 'company', 'string', 'Logo công ty');

            // Cài đặt hệ thống
            $this->set('site_title', 'Hệ thống Quản lý Vận tải HPL', 'system', 'string', 'Tiêu đề trang web');
            $this->set('pagination_limit', 15, 'system', 'integer', 'Số lượng bản ghi mỗi trang');
            $this->set('date_format', 'Y-m-d', 'system', 'string', 'Định dạng ngày tháng');
            $this->set('time_format', 'H:i', 'system', 'string', 'Định dạng thời gian');
            $this->set('timezone', 'Asia/Ho_Chi_Minh', 'system', 'string', 'Múi giờ');
            $this->set('maintenance_mode', false, 'system', 'boolean', 'Chế độ bảo trì');

            // Cài đặt vận chuyển
            $this->set('default_distance_unit', 'km', 'shipment', 'string', 'Đơn vị khoảng cách mặc định');
            $this->set('default_weight_unit', 'kg', 'shipment', 'string', 'Đơn vị trọng lượng mặc định');
            $this->set('auto_generate_shipment_code', true, 'shipment', 'boolean', 'Tự động tạo mã vận chuyển');
            $this->set('shipment_code_prefix', 'HPL', 'shipment', 'string', 'Tiền tố mã vận chuyển');

            // Cài đặt thông báo
            $this->set('email_notifications', true, 'notifications', 'boolean', 'Gửi thông báo qua email');
            $this->set('sms_notifications', false, 'notifications', 'boolean', 'Gửi thông báo qua SMS');
            $this->set('notification_email', 'notifications@vantaihpl.com', 'notifications', 'string', 'Email gửi thông báo');

            Log::info('Đã khởi tạo cài đặt mặc định thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi khởi tạo cài đặt mặc định: ' . $e->getMessage());
        }
    }
}
