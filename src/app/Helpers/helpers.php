<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('greeting_message')) {
    function greeting_message()
    {
        $now = Carbon::now();
        $hour = (int) $now->format('H');
        $dayOfWeek = (int) $now->format('N');

        $name = Auth::user()->full_name ?? null; 
        if (in_array($dayOfWeek, [6, 7])) {
            $greeting = __('messages.happy_weekend') . ' 🎉';
        }

        if ($hour >= 22 || $hour < 5) {
            $greeting = __('messages.good_night') . ' 🌙';
        }

        if ($hour >= 5 && $hour < 12) {
            $greeting = __('messages.good_morning') . ' ☀️';
        }

        if ($hour >= 12 && $hour < 18) {
            $greeting = __('messages.good_afternoon') . ' ☀️';
        }

        if ($hour >= 18 && $hour < 22) {
            $greeting = __('messages.good_evening') . ' 🌇';
        }

        return $greeting . ', ' . $name . '!';
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d')
    {
        return $date ? Carbon::parse($date)->format($format) : null;
    }
}

if (!function_exists('shorten_text')) {
    function shorten_text($text, $limit = 50)
    {
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }
}

if (!function_exists('months_list')) {
    /**
     * Trả về danh sách các tháng từ tháng hiện tại đến tháng trở về trước
     * 
     * @param int $monthsBack Số tháng trở về trước (mặc định là 12)
     * @param string $format Định dạng tháng (mặc định là 'm/Y')
     * @param bool $includeKeys Có bao gồm key là timestamp hay không
     * @return array Mảng danh sách các tháng
     */
    function months_list(int $monthsBack = 12, int $monthBefore = 0, string $format = 'm/Y', bool $includeKeys = false): array
    {
        $months = [];
        $currentDate = now();
        if ($monthBefore > 0) {
            for ($j = $monthBefore; $j > 0; $j--) {
                // Tính toán ngày đầu tiên của tháng
                $date = (clone $currentDate)->addMonthsNoOverflow($j);
                
                if ($includeKeys) {
                    // Sử dụng timestamp làm key
                    $months[$date->timestamp] = $date->format($format);
                } else {
                    // Mảng tuần tự không có key
                    $months[] = $date->format($format);
                }
            }
        }
        
        for ($i = 0; $i < $monthsBack; $i++) {
            // Tính toán ngày đầu tiên của tháng
            $date = (clone $currentDate)->subMonthsNoOverflow($i);
            
            if ($includeKeys) {
                // Sử dụng timestamp làm key
                $months[$date->timestamp] = $date->format($format);
            } else {
                // Mảng tuần tự không có key
                $months[] = $date->format($format);
            }
        }
        return $months;
    }
}

// Helper để format tiền tệ
if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'VND'): string
    {
        return number_format($amount, 0, ',', '.') . ' ' . $currency;
    }
}

// Helper để format trạng thái
if (!function_exists('quote_status_badge')) {
    function quote_status_badge($status): string
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Bản nháp</span>',
            'sent' => '<span class="badge bg-primary">Đã gửi</span>',
            'approved' => '<span class="badge bg-success">Đã duyệt</span>',
            'rejected' => '<span class="badge bg-danger">Từ chối</span>',
            'expired' => '<span class="badge bg-warning">Hết hạn</span>',
            'converted' => '<span class="badge bg-info">Đã chuyển đổi</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }
}

// Helper để format loại xe
if (!function_exists('vehicle_type_text')) {
    function vehicle_type_text($type): string
    {
        $types = [
            'truck' => 'Xe tải',
            'container' => 'Container',
            'motorcycle' => 'Xe máy',
            'van' => 'Xe tải nhỏ',
        ];

        return $types[$type] ?? $type;
    }
}

/**
 * Parse decimal number from string (supports both comma and dot as decimal separator)
 * Chuyển đổi string số thập phân từ dấu phẩy (,) hoặc dấu chấm (.) thành float
 * 
 * @param string|float|int $value
 * @return float
 */
function parseDecimal($value): float
{
    if (is_numeric($value)) {
        return (float) $value;
    }
    
    if (is_string($value)) {
        // Thay thế dấu phẩy bằng dấu chấm để parse đúng
        $normalized = str_replace(',', '.', $value);
        
        // Kiểm tra xem có phải số hợp lệ không
        if (is_numeric($normalized)) {
            return (float) $normalized;
        }
    }
    
    // Fallback về 0 nếu không parse được
    return 0.0;
}

/**
 * Calculate working days in a month (26-27 days depending on the month)
 * 
 * @param \Carbon\Carbon $startDate
 * @param \Carbon\Carbon $endDate
 * @return int
 */
function getWorkingDaysInMonth(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): int
{
    // Calculate the number of days in the month
    $daysInMonth = $startDate->daysInMonth;
    
    // Return 26 or 27 days based on the month length
    // Most months have 30-31 days, so we use 26-27 working days
    return $daysInMonth >= 31 ? 27 : 26;
}
