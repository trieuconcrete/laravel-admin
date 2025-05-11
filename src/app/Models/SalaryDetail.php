<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * Lấy thông tin nhân viên của bảng lương.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
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
        return $this->total_salary - $this->social_insurance - $this->health_insurance - 
               $this->income_tax - $this->other_deduction;
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
}
