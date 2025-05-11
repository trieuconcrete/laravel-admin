<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

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
}