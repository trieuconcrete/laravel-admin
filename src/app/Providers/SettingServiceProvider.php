<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('setting', function ($app) {
            return $app->make(SettingService::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Chia sẻ cài đặt công ty với tất cả các view
        try {
            $settingService = $this->app->make(SettingService::class);
            $companySettings = $settingService->getByGroup('company');
            
            if ($companySettings) {
                $companyData = [];
                foreach ($companySettings as $setting) {
                    $companyData[$setting->key] = $setting->value;
                }
                
                View::share('companySettings', $companyData);
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi khi chưa có bảng settings
            // Điều này cho phép các migration chạy mà không gặp lỗi
        }
    }
}
