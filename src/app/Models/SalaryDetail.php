<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Enum\SalaryType;
use Illuminate\Support\Facades\Log;

class SalaryDetail extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'salary_details';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'salary_id';

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'period_id',
        'base_salary',
        'working_days',
        'fuel_allowance',
        'meal_allowance',
        'other_allowance',
        'bonus',
        'penalty',
        'social_insurance',
        'health_insurance',
        'income_tax',
        'other_deduction',
        'total_salary',
        'net_salary',
        'status',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
        'approved_by',
        'total_allowance',
        'total_expenses',
        'salary_type', // 1: Tài xế ăn lương cơ bản, 2: Tài xế ăn lương doanh số
        'salary_by_percent' // Phần trăm lương theo doanh số - snapshot khi tạo kỳ lương
    ];

    /**
     * Các thuộc tính nên được chuyển đổi thành kiểu ngày tháng.
     *
     * @var array
     */
    protected $dates = [
        'payment_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'salary_type' => SalaryType::class,
        ];
    }

    /**
     * Lấy thông tin nhân viên của bảng lương.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * Lấy thông tin kỳ lương của bảng lương.
     */
    public function salaryPeriod()
    {
        return $this->belongsTo(SalaryPeriod::class, 'period_id', 'period_id');
    }

    /**
     * Lấy chi tiết các khoản phụ cấp.
     */
    public function allowanceDetails()
    {
        return $this->hasMany(AllowanceDetail::class, 'salary_id', 'salary_id');
    }

    /**
     * Lấy chi tiết các khoản khấu trừ.
     */
    public function deductionDetails()
    {
        return $this->hasMany(DeductionDetail::class, 'salary_id', 'salary_id');
    }

    /**
     * Lấy lịch sử thay đổi của bảng lương.
     */
    public function salaryHistory()
    {
        return $this->hasMany(SalaryHistory::class, 'salary_id', 'salary_id');
    }

    /**
     * Lấy các tệp đính kèm của bảng lương.
     */
    public function attachments()
    {
        return $this->hasMany(SalaryAttachment::class, 'salary_id', 'salary_id');
    }

    /**
     * Lấy thông tin người tạo bảng lương.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Lấy thông tin người phê duyệt bảng lương.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Tính tổng lương trước thuế.
     */
    public function calculateTotalSalary()
    {
        return $this->base_salary + $this->fuel_allowance + $this->meal_allowance + 
               $this->other_allowance + $this->bonus - $this->penalty;
    }

    /**
     * Tính lương thực nhận.
     */
    public function calculateNetSalary()
    {
        $netSalary = $this->total_salary - $this->social_insurance - $this->health_insurance - 
                     $this->income_tax - $this->other_deduction;
        
        // Debug log để kiểm tra giá trị
        \Log::info('SalaryDetail calculateNetSalary Debug', [
            'salary_id' => $this->salary_id,
            'total_salary' => $this->total_salary,
            'social_insurance' => $this->social_insurance,
            'health_insurance' => $this->health_insurance,
            'income_tax' => $this->income_tax,
            'other_deduction' => $this->other_deduction,
            'calculated_net_salary' => $netSalary,
            'final_net_salary' => max(0, $netSalary)
        ]);
        
        // Đảm bảo lương thực nhận không âm
        return max(0, $netSalary);
    }

    /**
     * Cập nhật và tính lại tổng lương và lương thực nhận.
     */
    public function recalculateSalary()
    {
        $this->total_salary = $this->calculateTotalSalary();
        $this->net_salary = $this->calculateNetSalary();
        $this->save();
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
     * Check if this is basic salary type
     *
     * @return bool
     */
    public function isBasicSalaryType(): bool
    {
        return $this->salary_type?->isBasicSalary() ?? true;
    }

    /**
     * Check if this is commission salary type
     *
     * @return bool
     */
    public function isCommissionSalaryType(): bool
    {
        return $this->salary_type?->isCommissionSalary() ?? false;
    }

    /**
     * Get commission percentage for this salary detail
     * 
     * @return float
     */
    public function getCommissionPercentage(): float
    {
        // Nếu không phải commission salary type thì return 0
        if (!$this->isCommissionSalaryType()) {
            return 0;
        }
        
        // Return giá trị salary_by_percent, mặc định 12% nếu null
        return (float) ($this->salary_by_percent ?? 12.00);
    }

    /**
     * Set commission percentage (chỉ cho commission salary type)
     * 
     * @param float|null $percent
     * @return void
     */
    public function setCommissionPercentage(?float $percent): void
    {
        if ($this->isCommissionSalaryType()) {
            $this->salary_by_percent = $percent;
        } else {
            $this->salary_by_percent = null;
        }
    }
}
