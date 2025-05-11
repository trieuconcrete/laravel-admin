<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'contract_code',
        'title',
        'customer_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'contact_position',
        'start_date',
        'end_date',
        'signing_date',
        'total_value',
        'currency',
        'payment_method',
        'payment_terms',
        'service_description',
        'status',
        'file_path',
        'attachment_paths',
        'notes',
        'termination_reason',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    /**
     * Các thuộc tính cần chuyển đổi.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signing_date' => 'date',
        'total_value' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Quan hệ với khách hàng
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Quan hệ với người tạo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Quan hệ với người cập nhật
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Quan hệ với người phê duyệt
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope để lọc hợp đồng theo trạng thái
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope để lọc hợp đồng đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope để lọc hợp đồng sắp hết hạn
     * @param int $days Số ngày trước khi hết hạn
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $date = now()->addDays($days);
        return $query->where('status', 'active')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<=', $date);
    }

    /**
     * Scope để tìm kiếm hợp đồng
     */
    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where(function($q) use ($term) {
                $q->where('contract_code', 'LIKE', "%{$term}%")
                  ->orWhere('title', 'LIKE', "%{$term}%")
                  ->orWhere('contact_name', 'LIKE', "%{$term}%")
                  ->orWhereHas('customer', function($subQuery) use ($term) {
                      $subQuery->where('name', 'LIKE', "%{$term}%");
                  });
            });
        }
        
        return $query;
    }

    /**
     * Kiểm tra xem hợp đồng có đang hoạt động hay không
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Kiểm tra xem hợp đồng có hết hạn hay không
     */
    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast() && $this->status === 'active';
    }

    /**
     * Tính số ngày còn lại của hợp đồng
     */
    public function daysRemaining()
    {
        if (!$this->end_date || !$this->isActive()) {
            return null;
        }
        
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Lấy danh sách trạng thái có thể
     */
    public static function statusList()
    {
        return [
            'draft' => 'Nháp',
            'pending' => 'Chờ phê duyệt',
            'active' => 'Đang hoạt động',
            'completed' => 'Hoàn thành',
            'terminated' => 'Chấm dứt',
            'cancelled' => 'Hủy'
        ];
    }

    /**
     * Lấy danh sách phương thức thanh toán
     */
    public static function paymentMethodList()
    {
        return [
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
            'credit_card' => 'Thẻ tín dụng',
            'other' => 'Khác'
        ];
    }
}