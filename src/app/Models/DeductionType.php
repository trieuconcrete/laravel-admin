<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeductionType extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'deduction_types';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'deduction_type_id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'type_name',
        'description',
        'is_mandatory',
        'status',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array
     */
    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    /**
     * Lấy các chi tiết khấu trừ thuộc loại này.
     */
    public function deductionDetails()
    {
        return $this->hasMany(DeductionDetail::class, 'deduction_type_id', 'deduction_type_id');
    }
}
