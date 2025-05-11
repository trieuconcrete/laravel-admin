<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllowanceType extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'allowance_types';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'allowance_type_id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'type_name',
        'description',
        'is_taxable',
        'status',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array
     */
    protected $casts = [
        'is_taxable' => 'boolean',
    ];

    /**
     * Lấy các chi tiết phụ cấp thuộc loại này.
     */
    public function allowanceDetails()
    {
        return $this->hasMany(AllowanceDetail::class, 'allowance_type_id', 'allowance_type_id');
    }
}
