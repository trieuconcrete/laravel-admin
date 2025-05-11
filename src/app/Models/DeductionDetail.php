<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeductionDetail extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'deduction_details';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'deduction_detail_id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'salary_id',
        'deduction_type_id',
        'amount',
        'notes',
    ];

    /**
     * Cho biết model có sử dụng cột timestamps không.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Các thuộc tính nên được chuyển đổi thành kiểu ngày tháng.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * Lấy thông tin bảng lương liên quan.
     */
    public function salaryDetail()
    {
        return $this->belongsTo(SalaryDetail::class, 'salary_id', 'salary_id');
    }

    /**
     * Lấy thông tin loại khấu trừ.
     */
    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class, 'deduction_type_id', 'deduction_type_id');
    }
}
