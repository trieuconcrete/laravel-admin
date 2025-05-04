<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Vietnamese license plate formats
        $cities = ['11', '12', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'];
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        
        $cityCode = $this->faker->randomElement($cities);
        $letter = $this->faker->randomElement($letters);
        $numbers = $this->faker->numberBetween(10000, 99999);
        
        $plateNumber = $cityCode . $letter . '-' . $numbers;

        // Get random vehicle type
        $vehicleType = VehicleType::inRandomOrder()->first();
        if (!$vehicleType) {
            $vehicleType = VehicleType::factory()->create();
        }

        // Set capacity based on vehicle type name
        $capacity = 1.0;
        if (strpos(strtolower($vehicleType->name), 'xe tải nhỏ') !== false) {
            $capacity = $this->faker->randomFloat(1, 1.5, 2.5);
        } elseif (strpos(strtolower($vehicleType->name), 'xe tải trung') !== false) {
            $capacity = $this->faker->randomFloat(1, 2.5, 8.0);
        } elseif (strpos(strtolower($vehicleType->name), 'xe tải lớn') !== false) {
            $capacity = $this->faker->randomFloat(1, 8.0, 15.0);
        } elseif (strpos(strtolower($vehicleType->name), 'container 20') !== false) {
            $capacity = 20.0;
        } elseif (strpos(strtolower($vehicleType->name), 'container 40') !== false) {
            $capacity = 40.0;
        } elseif (strpos(strtolower($vehicleType->name), 'xe máy') !== false) {
            $capacity = 0.1;
        }

        return [
            'plate_number' => $plateNumber,
            'vehicle_type_id' => $vehicleType->vehicle_type_id,
            'capacity' => $capacity,
            'manufactured_year' => $this->faker->numberBetween(2015, 2024),
            'status' => $this->faker->randomElement(['active', 'maintenance', 'inactive']),
        ];
    }

    /**
     * Indicate that the vehicle is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the vehicle is in maintenance.
     */
    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }

    /**
     * Indicate that the vehicle is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
