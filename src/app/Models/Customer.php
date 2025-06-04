<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_BUSINESS = 'business';

    /**
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'customer_code',
        'name',
        'type',
        'phone',
        'email',
        'address',
        'province',
        'district',
        'ward',
        'tax_code',
        'establishment_date',
        'website',
        'primary_contact_name',
        'primary_contact_phone',
        'primary_contact_email',
        'primary_contact_position',
        'notes',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * Các thuộc tính cần chuyển đổi.
     *
     * @var array
     */
    protected $casts = [
        'establishment_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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
     * Scope để lọc khách hàng theo loại
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope để lọc khách hàng theo trạng thái
     */
    public function scopeActive($query, $status = true)
    {
        return $query->where('is_active', $status);
    }

    /**
     * Scope để tìm kiếm khách hàng
     */
    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('customer_code', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('tax_code', 'LIKE', "%{$term}%");
            });
        }
        
        return $query;
    }

    /**
     * Summary of getTypes
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            self::TYPE_INDIVIDUAL => 'Cá nhân',
            self::TYPE_BUSINESS => 'Doanh nghiệp'
        ];
    }

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Summary of getStatusActives
     * @return array{active: string, inactive: string}
     */
    public static function getStatusActives()
    {
        return [
            self::STATUS_ACTIVE => 'Đang hoạt động',
            self::STATUS_INACTIVE => 'Không hoạt động'
        ];
    }

    /**
     * Summary of scopeIndividuals
     * @param mixed $query
     */
    public function scopeIndividuals($query)
    {
        return $query->where('type', self::TYPE_INDIVIDUAL);
    }

    /**
     * Summary of scopeBusinesses
     * @param mixed $query
     */
    public function scopeBusinesses($query)
    {
        return $query->where('type', self::TYPE_BUSINESS);
    }

    /**
     * Lấy nhãn hiển thị cho loại khách hàng
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::getTypes()[$this->type] ?? '';
    }

    /**
     * Lấy class CSS cho badge loại khách hàng
     *
     * @return string
     */
    public function getTypeBadgeClassAttribute()
    {
        return $this->type === self::TYPE_BUSINESS ? 'primary' : 'info';
    }

    /**
     * Lấy nhãn hiển thị cho trạng thái
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Đang hoạt động' : 'Không hoạt động';
    }

    /**
     * Lấy class CSS cho badge trạng thái
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * Kiểm tra xem khách hàng có phải là doanh nghiệp không
     *
     * @return bool
     */
    public function isBusiness()
    {
        return $this->type === self::TYPE_BUSINESS;
    }

    /**
     * Kiểm tra xem khách hàng có phải là cá nhân không
     *
     * @return bool
     */
    public function isIndividual()
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }

    /**
     * Lấy địa chỉ đầy đủ
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->address,
            $this->ward,
            $this->district,
            $this->province
        ]);
        
        return implode(', ', $addressParts);
    }

    /**
     * Lấy thông tin liên hệ chính
     *
     * @return string
     */
    public function getPrimaryContactAttribute()
    {
        if ($this->isBusiness() && $this->primary_contact_name) {
            $contact = $this->primary_contact_name;
            if ($this->primary_contact_position) {
                $contact .= ' (' . $this->primary_contact_position . ')';
            }
            return $contact;
        }
        
        return $this->name;
    }

    /**
     * Summary of getPrimaryPhoneAttribute
     */
    public function getPrimaryPhoneAttribute()
    {
        return $this->primary_contact_phone ?: $this->phone;
    }

    /**
     * Summary of getPrimaryEmailAttribute
     */
    public function getPrimaryEmailAttribute()
    {
        return $this->primary_contact_email ?: $this->email;
    }

    /**
     * Tạo mã khách hàng tự động
     *
     * @return string
     */
    public static function generateCustomerCode($type)
    {
        $prefix = $type == self::TYPE_INDIVIDUAL ? 'IND' : 'BUS';

        $lastCustomer = self::withTrashed()
            ->where('customer_code', 'LIKE', $prefix.'%')
            ->orderBy('customer_code', 'desc')
            ->withTrashed()
            ->first();


        if ($lastCustomer) {
            $lastNumber = (int) substr($lastCustomer->customer_code, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method để tự động tạo mã khách hàng
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = self::generateCustomerCode($customer->type);
            }
        });
    }

    /**
     * Lấy tổng số đơn hàng
     *
     * @return int
     */
    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    /**
     * Lấy tổng giá trị đơn hàng
     *
     * @return float
     */
    public function getTotalOrderValueAttribute()
    {
        return $this->orders()->sum('total_amount') ?? 0;
    }

    /**
     * Lấy đơn hàng gần nhất
     *
     * @return Order|null
     */
    public function getLatestOrderAttribute()
    {
        return $this->orders()->latest()->first();
    }

    /**
     * Lấy thống kê tóm tắt khách hàng
     *
     * @return array
     */
    public function getSummaryStats()
    {
        return [
            'total_orders' => $this->total_orders,
            'total_value' => $this->total_order_value,
            'latest_order_date' => $this->latest_order?->created_at,
            'is_active' => $this->is_active,
            'type' => $this->type_label
        ];
    }
}