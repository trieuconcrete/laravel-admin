<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    const TYPE_INCOME = 'income'; // thu
    const TYPE_EXPENSE = 'expense'; // chi

    const CATEGORY_ADVANCE_SALARY = 'advance_salary'; // ứng lương
    const CATEGORY_CUSTOMER_PAYMENT = 'customer_payment'; // thanh toán khách hàng
    const CATEGORY_CUSTOMER_REFUND = 'customer_refund'; // hoàn trả khách hàng
    const CATEGORY_OTHER = 'other'; // khác

    protected $fillable = [
        'payment_id',
        'type',
        'amount',
        'category',
        'description',
        'transaction_date',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }


    /**
     * Get all available types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_INCOME => 'Thu',
            self::TYPE_EXPENSE => 'Chi',
        ];
    }

    /**
     * Get the type label attribute.
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return $this->getTypes()[$this->type];
    }

    /**
     * Get the type badge class attribute.
     *
     * @return string
     */
    public function getTypeBadgeClassAttribute()
    {
        return match($this->type) {
            self::TYPE_INCOME => 'warning',
            self::TYPE_EXPENSE => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get all available categories
     *
     * @return array
     */
    public static function getCategories()
    {
        return [
            self::CATEGORY_ADVANCE_SALARY => 'Ứng lương',
            self::CATEGORY_CUSTOMER_PAYMENT => 'Khách hàng thanh toán',
            self::CATEGORY_CUSTOMER_REFUND => 'Khách hàng hoàn trả',
            self::CATEGORY_OTHER => 'Khác',
        ];
    }

    /**
     * Get the category label attribute.
     *
     * @return string
     */
    public function getCategoryLabelAttribute()
    {
        return $this->getCategories()[$this->category];
    }

    /**
     * Get the category badge class attribute.
     *
     * @return string
     */
    public function getCategoryBadgeClassAttribute()
    {
        return match($this->category) {
            self::CATEGORY_ADVANCE_SALARY => 'warning',
            self::CATEGORY_CUSTOMER_PAYMENT => 'success',
            self::CATEGORY_CUSTOMER_REFUND => 'danger',
            self::CATEGORY_OTHER => 'secondary',
            default => 'secondary'
        };
    }
}
