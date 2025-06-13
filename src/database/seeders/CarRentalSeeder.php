<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarRental;
use App\Models\CarRentalVehicle;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CarRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        // Lấy tất cả customer IDs từ bảng customers
        $customerIds = \DB::table('customers')->pluck('id')->toArray();
        
        // Kiểm tra nếu không có customers
        if (empty($customerIds)) {
            $this->command->error('No customers found! Please run CustomerSeeder first.');
            return;
        }

        // Lấy tất cả vehicle IDs từ bảng vehicles
        $vehicleIds = \DB::table('vehicles')->pluck('vehicle_id')->toArray();
        
        // Kiểm tra nếu không có vehicles
        if (empty($vehicleIds)) {
            $this->command->error('No vehicles found! Please run VehicleSeeder first.');
            return;
        }

        // Tạo 30 car rental records
        for ($i = 0; $i < 30; $i++) {
            $status = $faker->randomElement([
                CarRental::STATUS_PENDING,
                CarRental::STATUS_APPROVED,
                CarRental::STATUS_REJECTED,
                CarRental::STATUS_COMPLETED,
                CarRental::STATUS_CANCELLED,
            ]);
            
            $carRental = CarRental::create([
                'customer_id' => $faker->randomElement($customerIds), // Lấy ngẫu nhiên từ customers thực tế
                'status' => $status,
                'description' => $faker->randomElement([
                    'Thuê xe du lịch gia đình',
                    'Thuê xe đi công tác',
                    'Thuê xe cưới hỏi',
                    'Thuê xe đi sân bay',
                    'Thuê xe du lịch dài ngày',
                    'Thuê xe tham quan thành phố',
                    'Thuê xe đi tỉnh',
                    'Thuê xe sự kiện công ty',
                ]),
                'notes' => $faker->optional(0.7)->sentence(),
                'total_money' => 0, // Sẽ được tự động tính toán
                'file' => $faker->optional(0.3)->randomElement([
                    'contracts/contract_' . $faker->uuid . '.pdf',
                    'contracts/contract_' . $faker->uuid . '.docx',
                ]),
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
            
            // Tạo 1-3 vehicles cho mỗi rental
            $vehicleCount = $faker->numberBetween(1, 3);
            
            for ($j = 0; $j < $vehicleCount; $j++) {
                $startDate = Carbon::parse($carRental->created_at)->addDays($faker->numberBetween(1, 7));
                $unit = $faker->randomElement([
                    CarRentalVehicle::UNIT_MONTH,
                    CarRentalVehicle::UNIT_DAY,
                    CarRentalVehicle::UNIT_KM,
                    CarRentalVehicle::UNIT_TRIP,
                ]);
                
                // Xác định end date dựa trên unit
                $endDate = match($unit) {
                    CarRentalVehicle::UNIT_MONTH => $startDate->copy()->addMonths($faker->numberBetween(1, 6)),
                    CarRentalVehicle::UNIT_DAY => $startDate->copy()->addDays($faker->numberBetween(1, 30)),
                    CarRentalVehicle::UNIT_KM => $startDate->copy()->addDays($faker->numberBetween(1, 7)),
                    CarRentalVehicle::UNIT_TRIP => $startDate->copy()->addDays($faker->numberBetween(0, 3)),
                };
                
                // Xác định amount và price dựa trên unit
                list($amount, $price) = match($unit) {
                    CarRentalVehicle::UNIT_MONTH => [$faker->numberBetween(1, 6), $faker->randomElement([15000000, 18000000, 20000000, 25000000])],
                    CarRentalVehicle::UNIT_DAY => [$faker->numberBetween(1, 30), $faker->randomElement([800000, 1000000, 1200000, 1500000])],
                    CarRentalVehicle::UNIT_KM => [$faker->numberBetween(100, 1000), $faker->randomElement([5000, 8000, 10000, 12000])],
                    CarRentalVehicle::UNIT_TRIP => [$faker->numberBetween(1, 5), $faker->randomElement([500000, 800000, 1000000, 1500000])],
                };
                
                CarRentalVehicle::create([
                    'car_rental_id' => $carRental->id,
                    'vehicle_id' => $faker->randomElement($vehicleIds), // Lấy ngẫu nhiên từ vehicles thực tế
                    'product_name' => $faker->randomElement([
                        'Toyota Vios 1.5G',
                        'Toyota Camry 2.5Q',
                        'Toyota Innova 2.0G',
                        'Honda City 1.5L',
                        'Honda CR-V 1.5L',
                        'Mazda CX-5 2.0',
                        'Mazda 3 1.5L',
                        'Hyundai Accent 1.4',
                        'Hyundai Santa Fe 2.2',
                        'Kia Morning 1.25',
                        'Kia Sorento 2.4',
                        'Ford Ranger 2.2',
                        'Ford Everest 2.0',
                        'Mercedes-Benz C200',
                        'Mercedes-Benz S450',
                        'BMW 320i',
                        'BMW X5',
                        'Vinfast Lux A2.0',
                        'Vinfast Lux SA2.0',
                        'Suzuki XL7',
                    ]),
                    'unit' => $unit,
                    'amount' => $amount,
                    'price' => $price,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'notes' => $faker->optional(0.5)->sentence(),
                ]);
            }
        }
        
        $this->command->info('Car rental data seeded successfully!');
    }
}