<?php

namespace Database\Seeders;

use App\Models\DriverLicense;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverLicensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo đã có drivers
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        $drivers = User::where('role', 'driver')->get();

        // Phân bố loại bằng lái phổ biến đối với ngành vận tải
        $commonLicenseTypes = [
            DriverLicense::TYPE_B2 => 20,  // 20% tài xế
            DriverLicense::TYPE_C => 40,   // 40% tài xế
            DriverLicense::TYPE_D => 15,   // 15% tài xế
            DriverLicense::TYPE_E => 15,   // 15% tài xế
            DriverLicense::TYPE_F => 10    // 10% tài xế
        ];

        foreach ($drivers as $driver) {
            // Nếu tài xế đã có bằng lái, bỏ qua
            if ($driver->license()->count() > 0) {
                continue;
            }

            // Chọn loại bằng lái dựa trên tỷ lệ phân bố
            $licenseType = $this->getRandomLicenseType($commonLicenseTypes);

            // Trường hợp phổ biến: 85% còn hiệu lực, 10% sắp hết hạn, 5% hết hạn
            $random = rand(1, 100);
            
            if ($random <= 85) {
                DriverLicense::factory()->valid()->ofType($licenseType)->create([
                    'user_id' => $driver->id
                ]);
            } elseif ($random <= 95) {
                DriverLicense::factory()->expiringSoon()->ofType($licenseType)->create([
                    'user_id' => $driver->id
                ]);
            } else {
                DriverLicense::factory()->expired()->ofType($licenseType)->create([
                    'user_id' => $driver->id
                ]);
            }

            // 15% trường hợp tài xế có bằng phụ (thường là bằng cấp thấp hơn)
            if (rand(1, 100) <= 15) {
                $secondaryLicenseType = $this->getLowerLicenseType($licenseType);
                
                if ($secondaryLicenseType) {
                    // Bằng phụ thường được cấp trước bằng chính
                    DriverLicense::factory()->valid()->ofType($secondaryLicenseType)->create([
                        'user_id' => $driver->id,
                        'issue_date' => now()->subYears(rand(7, 12)),
                        'expiry_date' => now()->addYears(rand(3, 8))
                    ]);
                }
            }
        }

        // Tạo một số bằng lái cụ thể cho demo
        $demoDriver = User::first();
        
        if ($demoDriver) {
            // Bằng lái chính - loại C
            DriverLicense::create([
                'user_id' => $demoDriver->id,
                'license_number' => 'C' . rand(10000000, 99999999),
                'license_type' => DriverLicense::TYPE_C,
                'issue_date' => now()->subYears(5),
                'expiry_date' => now()->addYears(5),
                'issued_by' => 'Sở GTVT TP. Hồ Chí Minh',
                'license_file' => 'demo_license_c.pdf',
                'status' => DriverLicense::STATUS_VALID
            ]);
            
            // Bằng lái phụ - loại B2 (đã có trước bằng C)
            DriverLicense::create([
                'user_id' => $demoDriver->id,
                'license_number' => 'B' . rand(10000000, 99999999),
                'license_type' => DriverLicense::TYPE_B2,
                'issue_date' => now()->subYears(8),
                'expiry_date' => now()->addYears(2),
                'issued_by' => 'Sở GTVT TP. Hồ Chí Minh',
                'license_file' => 'demo_license_b2.pdf',
                'status' => DriverLicense::STATUS_VALID
            ]);
        }
    }

    /**
     * Get a random license type based on distribution
     * 
     * @param array $distribution
     * @return string
     */
    private function getRandomLicenseType($distribution)
    {
        $rand = rand(1, 100);
        $sum = 0;
        
        foreach ($distribution as $type => $percentage) {
            $sum += $percentage;
            if ($rand <= $sum) {
                return $type;
            }
        }
        
        // Mặc định nếu có lỗi trong phân bố
        return DriverLicense::TYPE_B2;
    }

    /**
     * Get a lower license type than the main one
     * 
     * @param string $mainLicenseType
     * @return string|null
     */
    private function getLowerLicenseType($mainLicenseType)
    {
        $hierarchy = [
            DriverLicense::TYPE_F => [DriverLicense::TYPE_E, DriverLicense::TYPE_C, DriverLicense::TYPE_B2],
            DriverLicense::TYPE_E => [DriverLicense::TYPE_D, DriverLicense::TYPE_C, DriverLicense::TYPE_B2],
            DriverLicense::TYPE_D => [DriverLicense::TYPE_C, DriverLicense::TYPE_B2],
            DriverLicense::TYPE_C => [DriverLicense::TYPE_B2, DriverLicense::TYPE_B1],
            DriverLicense::TYPE_B2 => [DriverLicense::TYPE_B1, DriverLicense::TYPE_A2],
            DriverLicense::TYPE_B1 => [DriverLicense::TYPE_A2, DriverLicense::TYPE_A1],
        ];
        
        if (isset($hierarchy[$mainLicenseType])) {
            $lowerTypes = $hierarchy[$mainLicenseType];
            return $lowerTypes[array_rand($lowerTypes)];
        }
        
        return null;
    }
}
