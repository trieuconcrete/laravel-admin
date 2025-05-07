<?php

namespace Database\Factories;

use App\Models\DriverLicense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverLicense>
 */
class DriverLicenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DriverLicense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $licenseTypes = [
            DriverLicense::TYPE_A1,
            DriverLicense::TYPE_A2,
            DriverLicense::TYPE_A3,
            DriverLicense::TYPE_A4,
            DriverLicense::TYPE_B1,
            DriverLicense::TYPE_B2,
            DriverLicense::TYPE_C,
            DriverLicense::TYPE_D,
            DriverLicense::TYPE_E,
            DriverLicense::TYPE_F
        ];

        // Tạo ngày phát hành và hết hạn
        $issueDate = $this->faker->dateTimeBetween('-5 years', '-1 month');
        
        // Bằng lái có hiệu lực 10 năm từ ngày cấp
        $expiryDate = clone $issueDate;
        $expiryDate->modify('+10 years');

        // Danh sách các nơi cấp giấy phép
        $issuingAuthorities = [
            'Sở GTVT TP. Hồ Chí Minh',
            'Sở GTVT TP. Hà Nội',
            'Sở GTVT TP. Đà Nẵng',
            'Sở GTVT TP. Cần Thơ',
            'Sở GTVT Tỉnh Bình Dương',
            'Sở GTVT Tỉnh Đồng Nai',
            'Sở GTVT Tỉnh Bà Rịa - Vũng Tàu',
            'Sở GTVT Tỉnh Hải Phòng',
            'Sở GTVT Tỉnh Khánh Hòa',
            'Sở GTVT Tỉnh Lâm Đồng'
        ];

        // Định dạng số giấy phép theo loại bằng lái
        $licenseType = $this->faker->randomElement($licenseTypes);
        $licenseNumber = match ($licenseType) {
            DriverLicense::TYPE_A1, 
            DriverLicense::TYPE_A2, 
            DriverLicense::TYPE_A3, 
            DriverLicense::TYPE_A4 => 'A' . $this->faker->numerify('##########'),
            DriverLicense::TYPE_B1,
            DriverLicense::TYPE_B2 => 'B' . $this->faker->numerify('##########'),
            DriverLicense::TYPE_C => 'C' . $this->faker->numerify('##########'),
            DriverLicense::TYPE_D => 'D' . $this->faker->numerify('##########'),
            DriverLicense::TYPE_E => 'E' . $this->faker->numerify('##########'),
            DriverLicense::TYPE_F => 'F' . $this->faker->numerify('##########'),
            default => 'GP' . $this->faker->numerify('##########'),
        };

        // Xác định trạng thái dựa trên ngày hết hạn
        $status = DriverLicense::STATUS_VALID;
        if ($expiryDate < now()) {
            $status = DriverLicense::STATUS_EXPIRED;
        } elseif ($expiryDate < now()->addDays(30)) {
            $status = DriverLicense::STATUS_EXPIRING_SOON;
        }

        return [
            'user_id' => User::factory(),
            'license_number' => $licenseNumber,
            'license_type' => $licenseType,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'issued_by' => $this->faker->randomElement($issuingAuthorities),
            'license_file' => $this->faker->optional(0.7)->lexify('license_????.pdf'),
            'status' => $status
        ];
    }

    /**
     * Indicate that the license is valid.
     */
    public function valid(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-5 years', '-1 month');
            $expiryDate = clone $issueDate;
            $expiryDate->modify('+10 years');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => DriverLicense::STATUS_VALID
            ];
        });
    }

    /**
     * Indicate that the license is expired.
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-15 years', '-10 years');
            $expiryDate = clone $issueDate;
            $expiryDate->modify('+10 years');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => DriverLicense::STATUS_EXPIRED
            ];
        });
    }

    /**
     * Indicate that the license is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(function (array $attributes) {
            $expiryDate = $this->faker->dateTimeBetween('+1 day', '+30 days');
            $issueDate = clone $expiryDate;
            $issueDate->modify('-10 years');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => DriverLicense::STATUS_EXPIRING_SOON
            ];
        });
    }

    /**
     * Indicate that the license is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DriverLicense::STATUS_REVOKED
        ]);
    }

    /**
     * Configure for a specific license type.
     */
    public function ofType($licenseType): static
    {
        return $this->state(fn (array $attributes) => [
            'license_type' => $licenseType
        ]);
    }
}
