<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'customer_id',
        'amount',
        'payment_date',
        'status',
        'notes',
        'method',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    /**
     * Get the transactions that owns the payment.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payment_id');
    }

    /**
     * Get the customer that owns the payment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_CASH = 'cash';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Chờ thanh toán',
            self::STATUS_COMPLETED => 'Đã thanh toán',
            self::STATUS_FAILED => 'Đã hủy',
            self::STATUS_REFUNDED => 'Đã hoàn trả'
        ];
    }

    /**
     * Get the status attribute.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        return match($value) {
            self::STATUS_PENDING => 'Chờ thanh toán',
            self::STATUS_COMPLETED => 'Đã thanh toán',
            self::STATUS_FAILED => 'Đã hủy',
            self::STATUS_REFUNDED => 'Đã hoàn trả',
            default => $value
        };
    }

    /**
     * Get the status badge class attribute.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get all available payment methods
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Chuyển khoản ngân hàng',
            self::PAYMENT_METHOD_CASH => 'Tiền mặt'
        ];
    }

    public function getMethodLabelAttribute()
    {
        return $this->getPaymentMethods()[$this->method] ?? $this->method;
    }

    public function getPaymentMethodBadgeClassAttribute()
    {
        return match($this->method) {
            self::PAYMENT_METHOD_BANK_TRANSFER => 'warning',
            self::PAYMENT_METHOD_CASH => 'success',
            default => 'secondary'
        };
    }
}
