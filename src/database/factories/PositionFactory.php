<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Position;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positionNames = [
            'Giám đốc' => 'GD',
            'Phó giám đốc' => 'PGD',
            'Trưởng phòng' => 'TP',
            'Phó phòng' => 'PP',
            'Kế toán trưởng' => 'KTT',
            'Kế toán viên' => 'KT',
            'Tài xế' => 'TX',
            'Nhân viên' => 'NV',
            'Điều phối viên' => 'DP',
            'Nhân viên kho' => 'NK',
            'Kỹ thuật viên' => 'KTV',
            'Chăm sóc khách hàng' => 'CSKH',
            'Phụ xe' => 'PX',
            'Bảo vệ' => 'BV',
            'Lễ tân' => 'LT'
        ];

        $departments = [
            'Ban giám đốc',
            'Quản lý',
            'Tài chính - Kế toán',
            'Vận hành',
            'Hành chính',
            'Kho vận',
            'Kỹ thuật',
            'Kinh doanh',
            'IT',
            'Nhân sự'
        ];
        
        $positionName = $this->faker->randomElement(array_keys($positionNames));
        $positionCode = $positionNames[$positionName];
        
        // Xác định mức lương phù hợp với vị trí
        switch ($positionName) {
            case 'Giám đốc':
            case 'Phó giám đốc':
                $salaryMin = 30000000;
                $salaryMax = 80000000;
                break;
            case 'Trưởng phòng':
            case 'Kế toán trưởng':
                $salaryMin = 15000000;
                $salaryMax = 40000000;
                break;
            case 'Phó phòng':
                $salaryMin = 12000000;
                $salaryMax = 30000000;
                break;
            case 'Kế toán viên':
            case 'Kỹ thuật viên':
            case 'Điều phối viên':
                $salaryMin = 8000000;
                $salaryMax = 15000000;
                break;
            case 'Tài xế':
                $salaryMin = 7000000;
                $salaryMax = 20000000;
                break;
            default:
                $salaryMin = 5000000;
                $salaryMax = 12000000;
                break;
        }

        return [
            'name' => $positionName,
            'code' => $positionCode,
            'description' => $this->faker->sentence(),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'department' => $this->faker->randomElement($departments),
            'is_active' => $this->faker->boolean(90), // 90% vị trí hoạt động
        ];
    }

    /**
     * Indicate that the position is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the position is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterMaking(function (Position $position) {
            // Nếu code bị trùng, tạo một code mới ngẫu nhiên
            // Điều này đảm bảo không có lỗi unique constraint khi tạo nhiều vị trí cùng lúc
            if (Position::where('code', $position->code)->exists()) {
                $position->code = $position->code . $this->faker->randomNumber(2);
            }
        });
    }
}
