<?php
namespace App\Helpers;

class NumberHelper
{
    /**
     * Remove formatting from number string
     * Convert "50,000" to 50000
     * 
     * @param mixed $value
     * @return float|null
     */
    public static function parseNumber($value): ?float
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        
        // Nếu đã là số
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Loại bỏ tất cả ký tự không phải số, dấu chấm hoặc dấu trừ
        $cleaned = preg_replace('/[^0-9.\-]/', '', $value);
        
        // Xử lý trường hợp có nhiều dấu chấm (format tiếng Việt: 1.000.000,50)
        $parts = explode('.', $cleaned);
        if (count($parts) > 2) {
            // Giữ lại dấu chấm cuối cùng làm dấu thập phân
            $lastPart = array_pop($parts);
            $cleaned = implode('', $parts) . '.' . $lastPart;
        }
        
        return (float) $cleaned;
    }
    
    /**
     * Format number to display with thousand separator
     * 
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    public static function formatNumber($value, $decimals = 0): string
    {
        if (is_null($value)) {
            return '0';
        }
        
        return number_format((float) $value, $decimals, '.', ',');
    }
}