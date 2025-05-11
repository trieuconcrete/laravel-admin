<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Department extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'department_id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'department_name',
        'description',
        'manager_id',
        'status',
    ];

    /**
     * Lấy tất cả nhân viên thuộc phòng ban này.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }

    /**
     * Lấy thông tin người quản lý phòng ban.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }

    /**
     * Lấy danh sách nhân viên đang hoạt động trong phòng ban.
     */
    public function activeEmployees()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id')
            ->where('status', 'active');
    }

    /**
     * Lấy số lượng nhân viên trong phòng ban.
     */
    public function employeeCount()
    {
        return $this->employees()->count();
    }

    /**
     * Lấy tổng chi phí lương của phòng ban trong kỳ lương cụ thể.
     */
    public function totalSalaryInPeriod($periodId)
    {
        return SalaryDetail::whereHas('employee', function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->where('period_id', $periodId)
            ->sum('total_salary');
    }

    /**
     * Lấy tổng lương thực nhận của phòng ban trong kỳ lương cụ thể.
     */
    public function totalNetSalaryInPeriod($periodId)
    {
        return SalaryDetail::whereHas('employee', function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->where('period_id', $periodId)
            ->sum('net_salary');
    }

    /**
     * Lấy lương trung bình của phòng ban trong kỳ lương cụ thể.
     */
    public function averageSalaryInPeriod($periodId)
    {
        $count = SalaryDetail::whereHas('employee', function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->where('period_id', $periodId)
            ->count();

        if ($count === 0) {
            return 0;
        }

        $total = $this->totalSalaryInPeriod($periodId);
        return $total / $count;
    }

    /**
     * Lấy danh sách các bảng lương của phòng ban trong kỳ lương cụ thể.
     */
    public function salaryDetailsInPeriod($periodId)
    {
        return SalaryDetail::whereHas('employee', function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->where('period_id', $periodId)
            ->with('employee')
            ->get();
    }

    /**
     * Kiểm tra xem phòng ban có phải là phòng ban tài xế hay không.
     * (Giả sử phòng ban có department_name chứa từ "tài xế" hoặc "driver" là phòng ban tài xế)
     */
    public function isDriverDepartment()
    {
        return str_contains(strtolower($this->department_name), 'tài xế') || 
               str_contains(strtolower($this->department_name), 'driver');
    }

    /**
     * Lấy danh sách phòng ban đang hoạt động (active).
     */
    public static function getActiveDepartments()
    {
        return self::where('status', 'active')->get();
    }

    /**
     * Lấy danh sách phòng ban dưới dạng mảng key-value cho dropdown.
     */
    public static function getDepartmentOptions()
    {
        return self::where('status', 'active')
            ->orderBy('department_name')
            ->pluck('department_name', 'department_id')
            ->toArray();
    }
}
