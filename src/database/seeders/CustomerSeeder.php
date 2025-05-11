<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (nếu cần)
        // DB::table('customers')->truncate();

        // Tạo mẫu khách hàng doanh nghiệp
        $this->createBusinessCustomers();
        
        // Tạo mẫu khách hàng cá nhân
        $this->createIndividualCustomers();

        // Customer::factory(10)->individual()->create();
        // Customer::factory(10)->business()->create();
    }

    /**
     * Tạo khách hàng doanh nghiệp
     */
    private function createBusinessCustomers(): void
    {
        $businesses = [
            [
                'name' => 'Công ty TNHH Vận tải Đông Á',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'primary_contact' => 'Nguyễn Văn An',
                'position' => 'Giám đốc vận tải',
            ],
            [
                'name' => 'Công ty Cổ phần Logistics Thái Dương',
                'province' => 'Hà Nội',
                'district' => 'Hai Bà Trưng',
                'primary_contact' => 'Trần Minh Đức',
                'position' => 'Trưởng phòng điều vận',
            ],
            [
                'name' => 'Công ty TNHH Thương mại Phúc Lợi',
                'province' => 'Đà Nẵng',
                'district' => 'Hải Châu',
                'primary_contact' => 'Lê Thị Hương',
                'position' => 'Giám đốc kinh doanh',
            ],
            [
                'name' => 'Tập đoàn Xuất nhập khẩu Hưng Thịnh',
                'province' => 'Bình Dương',
                'district' => 'Thuận An',
                'primary_contact' => 'Phạm Thanh Bình',
                'position' => 'Phó giám đốc',
            ],
            [
                'name' => 'Công ty Logistic Thành Công',
                'province' => 'Hải Phòng',
                'district' => 'Hồng Bàng',
                'primary_contact' => 'Hoàng Minh Tuấn',
                'position' => 'Trưởng phòng vận tải',
            ],
        ];

        foreach ($businesses as $index => $business) {
            Customer::create([
                'customer_code' => 'BUS' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'name' => $business['name'],
                'type' => 'business',
                'phone' => '0' . rand(900000000, 999999999),
                'email' => Str::slug(substr($business['name'], 11), '') . '@example.com',
                'address' => rand(1, 100) . ' ' . ['Đường Nguyễn Huệ', 'Đường Lê Lợi', 'Đường Trần Hưng Đạo', 'Đường Lý Thường Kiệt', 'Đường Phạm Ngũ Lão'][rand(0, 4)],
                'province' => $business['province'],
                'district' => $business['district'],
                'ward' => 'Phường ' . rand(1, 20),
                'tax_code' => rand(1000000000, 9999999999),
                'establishment_date' => Carbon::now()->subYears(rand(1, 20))->subMonths(rand(1, 12))->format('Y-m-d'),
                'website' => 'https://www.' . Str::slug(substr($business['name'], 11), '') . '.com.vn',
                'primary_contact_name' => $business['primary_contact'],
                'primary_contact_phone' => '09' . rand(10000000, 99999999),
                'primary_contact_email' => Str::slug(substr($business['primary_contact'], 0, 10), '') . '@' . Str::slug(substr($business['name'], 11), '') . '.com',
                'primary_contact_position' => $business['position'],
                'notes' => ['Khách hàng tiềm năng', 'Khách hàng thân thiết', 'Khách hàng VIP', 'Khách hàng mới', 'Cần chăm sóc thêm'][rand(0, 4)],
                'is_active' => rand(0, 10) > 2, // 80% cơ hội là hoạt động
                'created_by' => 1,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
        }
    }

    /**
     * Tạo khách hàng cá nhân
     */
    private function createIndividualCustomers(): void
    {
        $individuals = [
            [
                'name' => 'Trần Văn Bình',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 7',
            ],
            [
                'name' => 'Nguyễn Thị Mai',
                'province' => 'Hà Nội',
                'district' => 'Đống Đa',
            ],
            [
                'name' => 'Lê Minh Tuấn',
                'province' => 'Cần Thơ',
                'district' => 'Ninh Kiều',
            ],
            [
                'name' => 'Hoàng Thị Lan',
                'province' => 'Đà Nẵng',
                'district' => 'Sơn Trà',
            ],
            [
                'name' => 'Phạm Văn Hiếu',
                'province' => 'Nghệ An',
                'district' => 'Thành phố Vinh',
            ],
        ];

        foreach ($individuals as $index => $individual) {
            Customer::create([
                'customer_code' => 'IND' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'name' => $individual['name'],
                'type' => 'individual',
                'phone' => '09' . rand(10000000, 99999999),
                'email' => Str::slug($individual['name'], '') . rand(100, 999) . '@gmail.com',
                'address' => rand(1, 100) . ' ' . ['Đường Lê Duẩn', 'Đường Trần Phú', 'Đường Nguyễn Trãi', 'Đường Phan Đình Phùng', 'Đường Điện Biên Phủ'][rand(0, 4)],
                'province' => $individual['province'],
                'district' => $individual['district'],
                'ward' => 'Phường ' . rand(1, 15),
                'establishment_date' => Carbon::now()->subYears(rand(20, 50))->subMonths(rand(1, 12))->format('Y-m-d'), // Ngày sinh
                'notes' => ['Khách hàng thường xuyên', 'Khách hàng mới', 'Đã sử dụng dịch vụ nhiều lần', 'Khách hàng tiềm năng', ''][rand(0, 4)],
                'is_active' => rand(0, 10) > 1, // 90% cơ hội là hoạt động
                'created_by' => 1,
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
            ]);
        }
    }
}