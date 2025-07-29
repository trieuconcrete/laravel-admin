<?php

namespace App\Traits;

trait HasCompletedStatus
{
    /**
     * Kiểm tra xem có phải trạng thái hoàn thành hay không
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Kiểm tra xem có phải trạng thái chưa hoàn thành hay không
     */
    public function isNotCompleted(): bool
    {
        return !$this->isCompleted();
    }

    /**
     * Scope để lọc chỉ những record đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope để lọc chỉ những record chưa hoàn thành
     */
    public function scopeNotCompleted($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    /**
     * Tính tổng tiền chỉ cho những record đã hoàn thành
     */
    public static function sumCompletedAmount($amountColumn = 'amount')
    {
        return static::completed()->sum($amountColumn);
    }

    /**
     * Lấy danh sách record đã hoàn thành
     */
    public static function getCompleted()
    {
        return static::completed()->get();
    }

    /**
     * Lấy danh sách record chưa hoàn thành
     */
    public static function getNotCompleted()
    {
        return static::notCompleted()->get();
    }
} 