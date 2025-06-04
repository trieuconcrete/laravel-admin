<?php

namespace App\Http\Requests\Traits;

use App\Services\SettingService;
use Illuminate\Support\Facades\App;

trait UsesSystemDateFormat
{
    /**
     * Get the date format validation rule based on system settings
     *
     * @return string
     */
    protected function getSystemDateFormatRule()
    {
        $settingService = App::make(SettingService::class);
        $dateFormat = $settingService->get('date_format', 'd/m/Y');
        
        // Convert PHP date format to Laravel validation date_format pattern
        $formatMap = [
            'd' => 'dd',    // Day of the month, 2 digits with leading zeros
            'j' => 'd',     // Day of the month without leading zeros
            'm' => 'mm',    // Month, 2 digits with leading zeros
            'n' => 'm',     // Month without leading zeros
            'Y' => 'yyyy',  // A full numeric representation of a year, 4 digits
            'y' => 'yy',    // A two digit representation of a year
        ];
        
        $validationFormat = str_replace(
            array_keys($formatMap),
            array_values($formatMap),
            $dateFormat
        );
        
        return 'date|date_format:' . $validationFormat;
    }
}
