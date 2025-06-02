<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\DateHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the date helper as a blade directive
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::format($expression); ?>";
        });

        // Register the date helper for input fields
        Blade::directive('formatDateForInput', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatForInput($expression); ?>";
        });
    }
}
