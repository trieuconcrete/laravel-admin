<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SalaryAdvanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'salary_advance_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'request_code',
        'request_date',
        'amount',
        'advance_month',
        'reason',
        'status',
        'created_by',
        'updated_by',
        'type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'datetime',
        'advance_month' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';
    const STATUS_DEDUCTED = 'deducted';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_SALARY = 'salary';
    const TYPE_BONUS = 'bonus';
    const TYPE_PENALTY = 'penalty';
    const TYPE_OTHER = 'other';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Bản nháp',
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt',
            self::STATUS_REJECTED => 'Từ chối',
            self::STATUS_PAID => 'Đã chi',
            self::STATUS_DEDUCTED => 'Đã khấu trừ',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    
    /**
     * Get all available types
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SALARY => 'Ứng lương',
            self::TYPE_BONUS => 'Tiền thưởng',
            self::TYPE_PENALTY => 'Phạt',
            // self::TYPE_OTHER => 'Khác',
        ];
    }

    /**
     * Get type label attribute
     *
     * @return string
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? 'Ứng lương';
    }

    /**
     * Get type color attribute for UI
     *
     * @return string
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_SALARY => 'warning',
            self::TYPE_BONUS => 'success',
            self::TYPE_PENALTY => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the user that owns the salary advance request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the creator of the request.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the request.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the deductions for the salary advance request.
     */
    public function deductions(): HasMany
    {
        return $this->hasMany(SalaryAdvanceDeduction::class, 'salary_advance_request_id');
    }

    /**
     * Get status label attribute
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status color attribute for UI
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_PAID => 'info',
            self::STATUS_DEDUCTED => 'primary',
            self::STATUS_CANCELLED => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get total deducted amount
     *
     * @return float
     */
    public function getTotalDeductedAmountAttribute(): float
    {
        return $this->deductions->sum('deduction_amount');
    }

    /**
     * Get remaining amount to be deducted
     *
     * @return float
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->total_deducted_amount;
    }

    /**
     * Check if fully deducted
     *
     * @return bool
     */
    public function isFullyDeducted(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include paid requests.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to filter by advance month.
     */
    public function scopeAdvanceMonth($query, $month)
    {
        return $query->whereMonth('advance_month', Carbon::parse($month)->month)
                     ->whereYear('advance_month', Carbon::parse($month)->year);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate a unique request code
     *
     * @param string $type The request type
     * @return string
     */
    public static function generateRequestCode($type = self::TYPE_SALARY)
    {
        $date = now()->format('ymd');
        
        // Get prefix based on request type
        $prefix = match ($type) {
            self::TYPE_SALARY => 'UL',
            self::TYPE_BONUS => 'THUONG',
            self::TYPE_PENALTY => 'PHAT',
            default => 'UL',
        };
        
        // Find the last request with the same prefix and date
        $lastRequest = self::where('request_code', 'like', $prefix . $date . '%')
                           ->orderBy('id', 'desc')
                           ->first();
        
        // Check for existing requests with the same prefix
        if ($lastRequest && preg_match('/' . $prefix . $date . '(\d{5})/', $lastRequest->request_code, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $date . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto generate request code
        static::creating(function ($request) {
            if (empty($request->request_code)) {
                $request->request_code = self::generateRequestCode($request->type);
            }
            
            if (empty($request->request_date)) {
                $request->request_date = Carbon::now();
            }
        });

        // Update status when fully deducted
        static::updated(function ($request) {
            if ($request->status === self::STATUS_PAID && $request->isFullyDeducted()) {
                $request->update(['status' => self::STATUS_DEDUCTED]);
            }
        });
    }
}