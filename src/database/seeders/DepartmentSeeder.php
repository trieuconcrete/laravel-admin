<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'department_name' => 'Tài xế',
                'description' => 'Bộ phận tài xế vận chuyển hàng hóa',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Kỹ thuật',
                'description' => 'Bộ phận kỹ thuật bảo trì phương tiện',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Quản lý',
                'description' => 'Ban quản lý và điều hành',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Văn phòng',
                'description' => 'Bộ phận hành chính văn phòng',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Kế toán',
                'description' => 'Bộ phận kế toán tài chính',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Điều phối',
                'description' => 'Bộ phận điều phối lịch trình và đơn hàng',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Marketing',
                'description' => 'Bộ phận marketing và quan hệ khách hàng',
                'manager_id' => null,
                'status' => 'active',
            ],
            [
                'department_name' => 'Nhân sự',
                'description' => 'Bộ phận quản lý nhân sự',
                'manager_id' => null,
                'status' => 'active',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
        
        // Cập nhật người quản lý sau khi đã có dữ liệu nhân viên (nếu cần)
        // Đây chỉ là code mẫu, sẽ cần điều chỉnh ID nhân viên thực tế
        try {
            $driver = \App\Models\User::where('department_id', 1)->where('position_id', 1)->first();
            if ($driver) {
                Department::where('department_id', 1)->update(['manager_id' => $driver->id]);
            }
            
            $tech = \App\Models\User::where('department_id', 2)->where('position_id', 1)->first();
            if ($tech) {
                Department::where('department_id', 2)->update(['manager_id' => $tech->id]);
            }
            
            $manager = \App\Models\User::where('department_id', 3)->where('position_id', 1)->first();
            if ($manager) {
                Department::where('department_id', 3)->update(['manager_id' => $manager->id]);
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi nếu chưa có bảng Users hoặc dữ liệu chưa sẵn sàng
        }
    }
}
