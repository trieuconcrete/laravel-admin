<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;

class SetLocaleTimezone
{
    public function handle($request, Closure $next)
    {
        // $locale = App::getLocale();
        $locale = session('locale', config('app.locale'));
        if (in_array($locale, array_keys(config('languages.supported')))) {
            App::setLocale($locale);
        }
        $timezone = $this->getTimezoneByLocale($locale);
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $next($request);
    }

    private function getTimezoneByLocale($locale)
    {
        return match ($locale) {
            'vi' => 'Asia/Ho_Chi_Minh',
            'jp' => 'Asia/Tokyo',
            'en' => 'UTC',
            default => config('app.timezone'),
        };
    }
}
