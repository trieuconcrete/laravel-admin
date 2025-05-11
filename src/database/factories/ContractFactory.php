<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Tạo các ngày có ý nghĩa
        $signingDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $startDate = Carbon::parse($signingDate)->addDays($this->faker->numberBetween(1, 30));
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(90, 730));
        
        // Lấy danh sách khách hàng
        $customerId = Customer::inRandomOrder()->first()->id ?? 1;

        // Tạo mã hợp đồng
        $contractCode = 'CONTRACT-' . date('Ym', $signingDate->getTimestamp()) . '-' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Tạo giá trị hợp đồng
        $value = $this->faker->numberBetween(50000000, 2000000000);
        $value = round($value, -4); // Làm tròn đến hàng chục nghìn
        
        // Chọn trạng thái
        $status = $this->faker->randomElement(['draft', 'pending', 'active', 'completed', 'terminated', 'cancelled']);
        
        return [
            'contract_code' => $contractCode,
            'title' => 'Hợp đồng ' . $this->faker->randomElement(['vận chuyển', 'logistics', 'kho vận']) . ' - ' . $this->faker->company(),
            'customer_id' => $customerId,
            'contact_name' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
            'contact_position' => $this->faker->jobTitle(),
            'start_date' => $startDate,
            'end_date' => $status !== 'cancelled' ? $endDate : null,
            'signing_date' => $signingDate,
            'total_value' => $value,
            'currency' => 'VND',
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card', 'other']),
            'payment_terms' => $this->faker->paragraph(),
            'service_description' => $this->faker->paragraph(),
            'status' => $status,
            'file_path' => $this->faker->optional(0.3)->imageUrl(),
            'attachment_paths' => $this->faker->optional(0.2)->imageUrl(),
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'termination_reason' => $status === 'terminated' ? $this->faker->sentence() : null,
            'created_by' => 1,
            'updated_by' => $this->faker->optional(0.5, 1)->numberBetween(1, 3),
            'approved_by' => in_array($status, ['active', 'completed', 'terminated']) ? $this->faker->numberBetween(1, 3) : null,
            'approved_at' => in_array($status, ['active', 'completed', 'terminated']) ? Carbon::parse($startDate)->subDays($this->faker->numberBetween(1, 5)) : null,
        ];
    }

    /**
     * Cấu hình để tạo hợp đồng đang hoạt động
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'approved_by' => 1,
                'approved_at' => now()->subDays(rand(1, 30)),
            ];
        });
    }

    /**
     * Cấu hình để tạo hợp đồng sắp hết hạn
     */
    public function expiringSoon()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'end_date' => now()->addDays(rand(5, 30)),
                'approved_by' => 1,
                'approved_at' => now()->subDays(rand(30, 90)),
            ];
        });
    }

    /**
     * Cấu hình để tạo hợp đồng nháp
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    /**
     * Cấu hình để tạo hợp đồng vận chuyển container
     */
    public function containerShipping()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Hợp đồng vận chuyển container - ' . $this->faker->company(),
                'service_description' => 'Dịch vụ vận chuyển container từ cảng về kho và ngược lại.'
            ];
        });
    }
}