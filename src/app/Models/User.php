<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Position;
use App\Models\SalaryAdvanceRequest;
use App\Enum\SalaryType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_DRIVER = 'driver';
    const ROLE_STAFF = 'staff';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'birthday',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
        'employee_code',
        'position_id',
        'department_id',
        'salary_base',
        'id_number',
        'id_number_issuance_date',
        'profile_image',
        'address',
        'gender',
        'notes',
        'salary_advance_amount',
        'join_date',
        'salary_type',
        'salary_by_percent',
        'has_insurance',
        'insurance_start_date',
        'social_insurance_amount',
        'social_insurance_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'salary_type' => SalaryType::class,
            'has_insurance' => 'boolean',
            'insurance_start_date' => 'date',
            'social_insurance_amount' => 'decimal:0',
        ];
    }

    /**
     * Get all available roles
     *
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_MANAGER => 'Quản lý',
            self::ROLE_DRIVER => 'Tài xế',
            self::ROLE_STAFF => 'Nhân viên',
            self::ROLE_USER => 'Người dùng'
        ];
    }

    public function getGenderLabel(): string
    {
        return $this->gender ? 'Nam' : 'Nữ';
    }

    /**
     * Get the position that owns the user.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the driver that owns the license.
     */
    public function license(): HasOne
    {
        return $this->hasOne(DriverLicense::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->hasMany(Vehicle::class, 'driver_id');
    }

    /**
     * Generate employee code for the user
     *
     * @return string|null
     */
    public function generateEmployeeCode(): ?string
    {
        // Nếu đã có mã, không tạo mới
        if ($this->employee_code) {
            return $this->employee_code;
        }

        // Nếu người dùng không có chức vụ, không tạo mã
        if (!$this->position_id) {
            return null;
        }

        // Tạo mã mới
        $position = $this->position;
        $this->employee_code = $position->getNextEmployeeCode();
        $this->save();

        return $this->employee_code;
    }

    /**
     * Assign position to user
     *
     * @param int $positionId
     * @param bool $generateCode
     * @return bool
     */
    public function assignPosition($positionId, $generateCode = true): bool
    {
        $this->position_id = $positionId;
        $saved = $this->save();

        if ($saved && $generateCode) {
            $this->generateEmployeeCode();
        }

        return $saved;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        });
    }

    public function salaryAdvanceRequests()
    {
        return $this->hasMany(SalaryAdvanceRequest::class, 'user_id');
    }

    /**
     * Summary of getTotalSalaryAdvancesRequest
     * @param mixed $type
     * @param mixed $startDate
     * @param mixed $endDate
     */
    public function getTotalSalaryAdvancesRequest($type, $startDate, $endDate)
    {
        return $this->salaryAdvanceRequests()
            ->where('type', $type)
            ->whereBetween('advance_month', [$startDate, $endDate])
            ->whereIn('status', ['approved', SalaryAdvanceRequest::STATUS_PAID])
            ->sum('amount');
    }

    /**
     * Get salary advance requests by type for a date range
     * 
     * @param mixed $type
     * @param mixed $startDate
     * @param mixed $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSalaryAdvancesRequestByType($type, $startDate, $endDate)
    {
        return $this->salaryAdvanceRequests()
            ->where('type', $type)
            ->whereBetween('advance_month', [$startDate, $endDate])
            ->whereIn('status', ['approved', SalaryAdvanceRequest::STATUS_PAID])
            ->get();
    }

    /**
     * Get total amount of salary payments (payment type) for a date range
     * 
     * @param mixed $startDate
     * @param mixed $endDate
     * @return float
     */
    public function getTotalSalaryPayments($startDate, $endDate)
    {
        return $this->salaryAdvanceRequests()
            ->where('type', SalaryAdvanceRequest::TYPE_PAYMENT)
            ->whereBetween('advance_month', [$startDate, $endDate])
            ->whereIn('status', ['approved', SalaryAdvanceRequest::STATUS_PAID])
            ->sum('amount');
    }

    /**
     * Check if salary is fully paid for a specific month
     * 
     * @param string $month Format: m/Y (e.g., 07/2025)
     * @return array
     */
    public function isSalaryFullyPaid($month)
    {
        // Parse month and year
        list($monthNum, $year) = explode('/', $month);
        $startDate = Carbon::createFromDate($year, $monthNum, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $monthNum, 1)->endOfMonth();
        
        // Get salary detail for this month
        $periodName = 'Kỳ lương tháng ' . $month;
        $salaryPeriod = \App\Models\SalaryPeriod::where('period_name', $periodName)->first();
        
        if (!$salaryPeriod) {
            return [
                'is_fully_paid' => false,
                'reason' => 'Không tìm thấy kỳ lương cho tháng này',
                'net_salary' => 0,
                'total_paid' => 0,
                'remaining_amount' => 0
            ];
        }
        
        $salaryDetail = \App\Models\SalaryDetail::where('employee_id', $this->id)
            ->where('period_id', $salaryPeriod->period_id)
            ->first();
            
        if (!$salaryDetail) {
            return [
                'is_fully_paid' => false,
                'reason' => 'Không tìm thấy bảng lương cho tháng này',
                'net_salary' => 0,
                'total_paid' => 0,
                'remaining_amount' => 0
            ];
        }
        
        // Get total paid amount for this month
        $totalPaid = $this->getTotalSalaryPayments($startDate, $endDate);
        
        // Calculate remaining amount
        $netSalary = $salaryDetail->net_salary ?? 0;
        $remainingAmount = $netSalary - $totalPaid;
        
        return [
            'is_fully_paid' => $remainingAmount <= 0,
            'reason' => $remainingAmount <= 0 ? 'Đã thanh toán đủ lương' : 'Chưa thanh toán đủ lương',
            'net_salary' => $netSalary,
            'total_paid' => $totalPaid,
            'remaining_amount' => max(0, $remainingAmount),
            'salary_detail_id' => $salaryDetail->salary_id,
            'period_id' => $salaryPeriod->period_id
        ];
    }

    /**
     * Get salary type label
     *
     * @return string
     */
    public function getSalaryTypeLabelAttribute(): string
    {
        return $this->salary_type?->getLabel() ?? 'Không xác định';
    }

    /**
     * Get salary type color for UI
     *
     * @return string
     */
    public function getSalaryTypeColorAttribute(): string
    {
        return $this->salary_type?->getColor() ?? 'secondary';
    }

    /**
     * Check if user has basic salary type
     *
     * @return bool
     */
    public function isBasicSalaryType(): bool
    {
        return $this->salary_type?->isBasicSalary() ?? true;
    }

    /**
     * Check if user is eligible for lunch allowance
     * (Basic salary type and not a driver)
     *
     * @return bool
     */
    public function isEligibleForLunchAllowance(): bool
    {
        return $this->isBasicSalaryType() && $this->role !== self::ROLE_DRIVER;
    }

    /**
     * Get daily lunch allowance amount
     * Default: 35,000 VND per day
     *
     * @return float
     */
    public function getDailyLunchAllowance(): float
    {
        return 35000.00;
    }

    /**
     * Get monthly lunch allowance amount
     * Default: 35,000 VND × 22 working days = 770,000 VND
     *
     * @return float
     */
    public function getMonthlyLunchAllowance(): float
    {
        return $this->getDailyLunchAllowance() * 22;
    }

    /**
     * Check if user has commission salary type
     *
     * @return bool
     */
    public function isCommissionSalaryType(): bool
    {
        return $this->salary_type?->isCommissionSalary() ?? false;
    }

    /**
     * Get all available salary types
     *
     * @return array
     */
    public static function getSalaryTypes(): array
    {
        return SalaryType::getTypes();
    }

    /**
     * Get salary by percent for commission calculation
     * 
     * @return float
     */
    public function getSalaryByPercent(): float
    {
        // Nếu user không có loại lương commission thì return 0
        if (!$this->isCommissionSalaryType()) {
            return 0;
        }
        
        // Return giá trị salary_by_percent, mặc định 12% nếu null
        return (float) ($this->salary_by_percent ?? 12.00);
    }

    /**
     * Set salary by percent (chỉ cho commission salary type)
     * 
     * @param float|null $percent
     * @return void
     */
    public function setSalaryByPercent(?float $percent): void
    {
        if ($this->isCommissionSalaryType()) {
            $this->salary_by_percent = $percent;
        } else {
            $this->salary_by_percent = null;
        }
    }

    /**
     * Check if user has insurance
     *
     * @return bool
     */
    public function hasInsurance(): bool
    {
        return $this->has_insurance ?? true; // Default to true for backward compatibility
    }

    /**
     * Check if user should pay insurance for a specific period
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return bool
     */
    public function shouldPayInsuranceForPeriod(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): bool
    {
        if (!$this->hasInsurance()) {
            return false;
        }

        // If no insurance start date, assume they should pay
        if (!$this->insurance_start_date) {
            return true;
        }

        // Check if insurance start date is before or during the period
        return $this->insurance_start_date->lte($endDate);
    }

    /**
     * Get insurance status label
     *
     * @return string
     */
    public function getInsuranceStatusLabel(): string
    {
        if (!$this->hasInsurance()) {
            return 'Chưa đóng bảo hiểm';
        }

        if ($this->insurance_start_date) {
            return 'Đã đóng bảo hiểm từ ' . $this->insurance_start_date->format('d/m/Y');
        }

        return 'Đã đóng bảo hiểm';
    }

    /**
     * Get insurance status color for UI
     *
     * @return string
     */
    public function getInsuranceStatusColor(): string
    {
        if (!$this->hasInsurance()) {
            return 'danger';
        }

        return 'success';
    }

    /**
     * Get social insurance amount for this user
     * If not set, fallback to system setting
     *
     * @return float
     */
    public function getSocialInsuranceAmount(): float
    {
        if ($this->social_insurance_amount !== null) {
            return (float) $this->social_insurance_amount;
        }

        // Fallback to system setting
        return parseDecimal(\App\Models\Setting::get('social_insurance_contribution_amount', 5500000));
    }

    /**
     * Get social insurance amount label for display
     *
     * @return string
     */
    public function getSocialInsuranceAmountLabel(): string
    {
        $amount = $this->getSocialInsuranceAmount();
        $isCustom = $this->social_insurance_amount !== null;
        
        return number_format($amount) . ' đ' . ($isCustom ? ' (Cá nhân)' : ' (Hệ thống)');
    }
}
