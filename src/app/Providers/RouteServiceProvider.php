<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Http\Middleware\SetLocaleTimezone;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Add Middleware vào group 'web'
        // Route::pushMiddlewareToGroup('web', SetLocaleTimezone::class);

        // Các config khác như cũ
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
