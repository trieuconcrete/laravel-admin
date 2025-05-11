<?php

// Model: SalaryPeriod.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPeriod extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'salary_periods';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'period_id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'period_name',
        'start_date',
        'end_date',
        'payment_date',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * Các thuộc tính nên được chuyển đổi thành kiểu ngày tháng.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'payment_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Lấy tất cả chi tiết lương thuộc kỳ lương này.
     */
    public function salaryDetails()
    {
        return $this->hasMany(SalaryDetail::class, 'period_id', 'period_id');
    }

    /**
     * Lấy thông tin người tạo kỳ lương.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}