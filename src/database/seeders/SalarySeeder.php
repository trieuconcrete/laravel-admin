<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AllowanceDetail;
use App\Models\DeductionDetail;
use App\Models\SalaryDetail;
use App\Models\SalaryPeriod;
use App\Models\User;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các kỳ lương đã tạo
        $periods = SalaryPeriod::orderBy('start_date', 'desc')->get();
        
        // Lấy danh sách nhân viên
        $employees = User::get();
        $createBy = User::first()->id;
        
        // Tạo bảng lương cho từng nhân viên trong mỗi kỳ lương
        foreach ($periods as $period) {
            foreach ($employees as $employee) {
                // Giả định lấy lương cơ bản từ bảng position
                $baseSalary = $employee->position->base_salary ?? 10000000; // Giá trị mặc định
                
                // Tính các khoản phụ cấp mặc định
                $fuelAllowance = $employee->department_id == 1 ? 1500000 : 800000; // Giả sử bộ phận ID 1 là tài xế
                $mealAllowance = 1000000;
                $otherAllowance = $employee->department_id == 4 ? 1200000 : 500000; // Giả sử bộ phận ID 4 là quản lý
                
                // Tính các khoản khấu trừ mặc định
                $socialInsurance = $baseSalary * 0.08;
                $healthInsurance = $baseSalary * 0.015;
                $incomeTax = 0;
                
                // Tính thuế TNCN cơ bản nếu thu nhập đủ lớn (đơn giản hóa)
                $totalIncome = $baseSalary + $fuelAllowance + $mealAllowance + $otherAllowance;
                if ($totalIncome > 11000000) {
                    $taxableIncome = $totalIncome - 11000000; // Giả định miễn trừ gia cảnh 11 triệu
                    $incomeTax = $taxableIncome * 0.05; // Giả định mức thuế 5% cho đơn giản
                }
                
                // Thêm bảng lương chính
                $workingDays = rand(20, 22); // Ngẫu nhiên số ngày làm việc
                $bonus = 0;
                $penalty = 0;
                
                // Thêm thưởng/phạt ngẫu nhiên nếu là kỳ lương cũ (đã đóng)
                if ($period->status === 'closed') {
                    $bonus = rand(0, 10) > 3 ? rand(500000, 3000000) : 0; // 70% cơ hội có thưởng
                    $penalty = rand(0, 10) > 7 ? rand(200000, 800000) : 0; // 30% cơ hội có phạt
                }
                
                // Tính tổng lương và lương thực nhận
                $totalSalary = $baseSalary + $fuelAllowance + $mealAllowance + $otherAllowance + $bonus;
                $netSalary = $totalSalary - $socialInsurance - $healthInsurance - $incomeTax - $penalty;
                
                // Xác định trạng thái bảng lương dựa trên trạng thái kỳ lương
                $salaryStatus = 'draft';
                $paymentDate = null;
                $paymentMethod = null;
                $approvedBy = null;
                
                if ($period->status === 'processing') {
                    $salaryStatus = rand(0, 1) ? 'pending' : 'approved';
                    if ($salaryStatus === 'approved') {
                        $approvedBy = $createBy;
                    }
                } elseif ($period->status === 'closed') {
                    $salaryStatus = 'paid';
                    $paymentDate = $period->payment_date;
                    $paymentMethod = rand(0, 1) ? 'bank_transfer' : 'cash';
                    $approvedBy = $createBy;
                }
                
                // Tạo bảng lương
                $salaryDetail = SalaryDetail::create([
                    'employee_id' => $employee->user_id,
                    'period_id' => $period->period_id,
                    'base_salary' => $baseSalary,
                    'working_days' => $workingDays,
                    'fuel_allowance' => $fuelAllowance,
                    'meal_allowance' => $mealAllowance,
                    'other_allowance' => $otherAllowance,
                    'bonus' => $bonus,
                    'penalty' => $penalty,
                    'social_insurance' => $socialInsurance,
                    'health_insurance' => $healthInsurance,
                    'income_tax' => $incomeTax,
                    'other_deduction' => 0,
                    'total_salary' => $totalSalary,
                    'net_salary' => $netSalary,
                    'status' => $salaryStatus,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentMethod,
                    'notes' => 'Bảng lương tháng ' . date('m/Y', strtotime($period->start_date)),
                    'created_by' => $createBy,
                    'approved_by' => $approvedBy,
                ]);
                
                // Thêm chi tiết phụ cấp
                if ($fuelAllowance > 0) {
                    AllowanceDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'allowance_type_id' => 1, // ID của phụ cấp xăng xe
                        'amount' => $fuelAllowance,
                        'notes' => 'Phụ cấp xăng xe tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                if ($mealAllowance > 0) {
                    AllowanceDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'allowance_type_id' => 2, // ID của phụ cấp ăn trưa
                        'amount' => $mealAllowance,
                        'notes' => 'Phụ cấp ăn trưa tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                if ($otherAllowance > 0) {
                    AllowanceDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'allowance_type_id' => $employee->department_id == 4 ? 4 : 3, // ID của phụ cấp trách nhiệm hoặc điện thoại
                        'amount' => $otherAllowance,
                        'notes' => 'Phụ cấp khác tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                // Thêm chi tiết khấu trừ
                if ($socialInsurance > 0) {
                    DeductionDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'deduction_type_id' => 1, // ID của BHXH
                        'amount' => $socialInsurance,
                        'notes' => 'BHXH 8% tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                if ($healthInsurance > 0) {
                    DeductionDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'deduction_type_id' => 2, // ID của BHYT
                        'amount' => $healthInsurance,
                        'notes' => 'BHYT 1.5% tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                if ($incomeTax > 0) {
                    DeductionDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'deduction_type_id' => 4, // ID của Thuế TNCN
                        'amount' => $incomeTax,
                        'notes' => 'Thuế TNCN tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
                
                if ($penalty > 0) {
                    $penaltyType = rand(0, 1) ? 6 : 7; // ID của phạt trễ giờ hoặc phạt vi phạm
                    DeductionDetail::create([
                        'salary_id' => $salaryDetail->salary_id,
                        'deduction_type_id' => $penaltyType,
                        'amount' => $penalty,
                        'notes' => 'Phạt tháng ' . date('m/Y', strtotime($period->start_date)),
                    ]);
                }
            }
        }
    }
}
