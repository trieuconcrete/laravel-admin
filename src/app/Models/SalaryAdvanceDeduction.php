<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SalaryAdvanceDeduction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'salary_advance_deductions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'salary_advance_request_id',
        'deduction_month',
        'deduction_amount',
        'deduction_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deduction_amount' => 'decimal:2',
        'deduction_month' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Deduction type constants
     */
    const TYPE_SALARY = 'salary';
    const TYPE_BONUS = 'bonus';
    const TYPE_OTHER = 'other';

    /**
     * Get all available deduction types
     *
     * @return array
     */
    public static function getDeductionTypes(): array
    {
        return [
            self::TYPE_SALARY => 'Lương',
            self::TYPE_BONUS => 'Thưởng',
            self::TYPE_OTHER => 'Khác',
        ];
    }

    /**
     * Get the salary advance request that owns the deduction.
     */
    public function salaryAdvanceRequest(): BelongsTo
    {
        return $this->belongsTo(SalaryAdvanceRequest::class, 'salary_advance_request_id');
    }

    /**
     * Get the creator of the deduction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the deduction.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get deduction type label attribute
     *
     * @return string
     */
    public function getDeductionTypeLabelAttribute(): string
    {
        return self::getDeductionTypes()[$this->deduction_type] ?? $this->deduction_type;
    }

    /**
     * Get formatted deduction month attribute
     *
     * @return string
     */
    public function getFormattedDeductionMonthAttribute(): string
    {
        return Carbon::parse($this->deduction_month)->format('m/Y');
    }

    /**
     * Scope a query to filter by deduction month.
     */
    public function scopeDeductionMonth($query, $month)
    {
        return $query->whereMonth('deduction_month', Carbon::parse($month)->month)
                     ->whereYear('deduction_month', Carbon::parse($month)->year);
    }

    /**
     * Scope a query to filter by deduction type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('deduction_type', $type);
    }

    /**
     * Scope a query to filter by salary advance request.
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('salary_advance_request_id', $requestId);
    }

    /**
     * Calculate total deductions for a specific month
     *
     * @param int $userId
     * @param string $month (Y-m format)
     * @return float
     */
    public static function getTotalDeductionsForUserInMonth($userId, $month): float
    {
        return self::whereHas('salaryAdvanceRequest', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->deductionMonth($month)
                ->sum('deduction_amount');
    }

    /**
     * Get pending deductions for user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPendingDeductionsForUser($userId)
    {
        return self::whereHas('salaryAdvanceRequest', function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->where('status', SalaryAdvanceRequest::STATUS_PAID);
                })
                ->where('deduction_month', '>=', Carbon::now()->startOfMonth())
                ->orderBy('deduction_month')
                ->get();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update parent request status after creating deduction
        static::created(function ($deduction) {
            $request = $deduction->salaryAdvanceRequest;
            
            // Check if fully deducted
            if ($request->isFullyDeducted()) {
                $request->update(['status' => SalaryAdvanceRequest::STATUS_DEDUCTED]);
            }
        });

        // Ensure deduction amount doesn't exceed remaining amount
        static::creating(function ($deduction) {
            $request = $deduction->salaryAdvanceRequest;
            $remainingAmount = $request->remaining_amount;
            
            if ($deduction->deduction_amount > $remainingAmount) {
                $deduction->deduction_amount = $remainingAmount;
            }
        });
    }
}