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
    function format_date($date, $format = 'd/m/Y')
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
    function months_list(int $monthsBack = 12, string $format = 'm/Y', bool $includeKeys = false): array
    {
        $months = [];
        $currentDate = now();
        
        for ($i = 0; $i < $monthsBack; $i++) {
            // Tính toán ngày đầu tiên của tháng
            $date = (clone $currentDate)->subMonths($i)->startOfMonth();
            
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

