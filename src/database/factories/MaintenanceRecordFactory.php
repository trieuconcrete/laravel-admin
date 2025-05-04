<?php

namespace Database\Factories;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MaintenanceRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maintenanceTypes = array_keys(MaintenanceRecord::getMaintenanceTypes());
        $statuses = array_keys(MaintenanceRecord::getStatuses());
        
        $status = $this->faker->randomElement($statuses);
        $startDate = $this->faker->dateTimeBetween('-1 year', '+3 months');
        
        $endDate = null;
        if (in_array($status, [MaintenanceRecord::STATUS_COMPLETED, MaintenanceRecord::STATUS_CANCELLED])) {
            $endDate = $this->faker->dateTimeBetween($startDate, 'now');
        }

        $serviceProviders = [
            'Garage Minh Phát',
            'Garage Thành Đạt',
            'Trung tâm bảo dưỡng Honda',
            'Garage Hoàng Long',
            'Xưởng dịch vụ Phú Thịnh',
            'Garage Tuấn Anh',
            'Trung tâm sửa chữa Đông Á',
            'Garage Việt Hưng'
        ];

        return [
            'vehicle_id' => Vehicle::factory(),
            'maintenance_type' => $this->faker->randomElement($maintenanceTypes),
            'description' => $this->faker->sentence(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cost' => $status === MaintenanceRecord::STATUS_COMPLETED ? $this->faker->randomFloat(2, 500000, 10000000) : null,
            'service_provider' => $this->faker->randomElement($serviceProviders),
            'status' => $status,
            'notes' => $this->faker->optional(0.3)->paragraph(),
        ];
    }

    /**
     * Indicate that the maintenance is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceRecord::STATUS_SCHEDULED,
            'start_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'end_date' => null,
            'cost' => null,
        ]);
    }

    /**
     * Indicate that the maintenance is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceRecord::STATUS_IN_PROGRESS,
            'start_date' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'end_date' => null,
            'cost' => null,
        ]);
    }

    /**
     * Indicate that the maintenance is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-6 months', '-1 week');
            $endDate = $this->faker->dateTimeBetween($startDate, '-1 day');
            
            return [
                'status' => MaintenanceRecord::STATUS_COMPLETED,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'cost' => $this->faker->randomFloat(2, 500000, 10000000),
            ];
        });
    }

    /**
     * Indicate that the maintenance is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceRecord::STATUS_CANCELLED,
            'notes' => 'Lý do hủy: ' . $this->faker->sentence(),
        ]);
    }
}
