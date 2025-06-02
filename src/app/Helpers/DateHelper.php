<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class DateHelper
{
    /**
     * Format a date according to the system settings
     *
     * @param \Carbon\Carbon|string|null $date
     * @param string|null $defaultFormat
     * @return string|null
     */
    public static function format($date, $defaultFormat = null)
    {
        if (empty($date)) {
            return null;
        }

        // Convert string to Carbon if needed
        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return $date;
            }
        }

        // Get date format from settings
        $settingService = App::make('App\Services\SettingService');
        $format = $settingService->get('date_format', $defaultFormat ?: 'd/m/Y');

        return $date->format($format);
    }

    /**
     * Format a date for input fields (always Y-m-d)
     *
     * @param \Carbon\Carbon|string|null $date
     * @return string|null
     */
    public static function formatForInput($date)
    {
        if (empty($date)) {
            return null;
        }

        // Convert string to Carbon if needed
        if (is_string($date)) {
            try {
                $date = \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                return $date;
            }
        }

        // Input fields always need Y-m-d format
        return $date->format('Y-m-d');
    }

    /**
     * Get the month/year format based on system settings
     * 
     * @return string
     */
    public static function getMonthYearFormat()
    {
        // Get date format from settings
        $settingService = App::make('App\Services\SettingService');
        $dateFormat = $settingService->get('date_format', 'd/m/Y');
        
        // Extract month/year format from date format
        // Common formats: d/m/Y, m/d/Y, Y-m-d
        if (strpos($dateFormat, 'Y-m') !== false) {
            return 'm/Y'; // For Y-m-d format
        } elseif (strpos($dateFormat, 'm/d') !== false) {
            return 'm/Y'; // For m/d/Y format
        } else {
            return 'm/Y'; // Default for d/m/Y format
        }
    }
}
