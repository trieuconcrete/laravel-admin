<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basePrice = $this->faker->numberBetween(5000000, 50000000);
        $fuelSurcharge = $basePrice * 0.1;
        $loadingFee = $this->faker->numberBetween(500000, 2000000);
        $insuranceFee = $basePrice * 0.02;
        $additionalFee = $this->faker->numberBetween(0, 1000000);
        $discount = $this->faker->numberBetween(0, $basePrice * 0.05);

        $totalPrice = $basePrice + $fuelSurcharge + $loadingFee + $insuranceFee + $additionalFee - $discount;
        $vatRate = 10.00;
        $vatAmount = $totalPrice * ($vatRate / 100);
        $finalPrice = $totalPrice + $vatAmount;

        $pickupDate = $this->faker->dateTimeBetween('now', '+30 days');
        $deliveryDate = Carbon::instance($pickupDate)->addHours($this->faker->numberBetween(4, 48));

        return [
            'customer_name' => $this->faker->company(),
            'customer_phone' => $this->faker->phoneNumber(),
            'customer_email' => $this->faker->companyEmail(),
            'customer_address' => $this->faker->address(),
            'pickup_address' => $this->faker->address(),
            'delivery_address' => $this->faker->address(),
            'distance' => $this->faker->randomFloat(2, 10, 500),
            'cargo_weight' => $this->faker->randomFloat(2, 1, 20),
            'cargo_volume' => $this->faker->randomFloat(2, 5, 100),
            'cargo_type' => $this->faker->randomElement(['Thực phẩm', 'Điện tử', 'May mặc', 'Hóa chất', 'Xây dựng', 'Nông sản']),
            'cargo_description' => $this->faker->text(200),
            'vehicle_type' => $this->faker->randomElement(['truck', 'container', 'van', 'motorcycle']),
            'vehicle_quantity' => $this->faker->numberBetween(1, 3),
            'pickup_datetime' => $pickupDate,
            'delivery_datetime' => $deliveryDate,
            'is_round_trip' => $this->faker->boolean(),
            'base_price' => $basePrice,
            'fuel_surcharge' => $fuelSurcharge,
            'loading_fee' => $loadingFee,
            'insurance_fee' => $insuranceFee,
            'additional_fee' => $additionalFee,
            'additional_fee_description' => $additionalFee > 0 ? $this->faker->sentence() : null,
            'discount' => $discount,
            'total_price' => $totalPrice,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'final_price' => $finalPrice,
            'status' => $this->faker->randomElement(['draft', 'sent', 'approved', 'rejected', 'expired']),
            'valid_until' => $this->faker->dateTimeBetween('now', '+30 days'),
            'notes' => $this->faker->optional()->text(),
            'terms_conditions' => $this->getTermsConditions(),
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
        ];
    }

    /**
     * Indicate that the quote is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the quote has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    /**
     * Indicate that the quote is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the quote is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'valid_until' => $this->faker->dateTimeBetween('-10 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the quote is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'valid_until' => $this->faker->dateTimeBetween('now', '+7 days'),
        ]);
    }

    /**
     * Indicate that the quote is for truck transport.
     */
    public function forTruck(): static
    {
        return $this->state(fn (array $attributes) => [
            'vehicle_type' => 'truck',
            'cargo_weight' => $this->faker->randomFloat(2, 5, 20),
        ]);
    }

    /**
     * Indicate that the quote is for container transport.
     */
    public function forContainer(): static
    {
        return $this->state(fn (array $attributes) => [
            'vehicle_type' => 'container',
            'cargo_weight' => $this->faker->randomFloat(2, 10, 25),
            'base_price' => $this->faker->numberBetween(15000000, 50000000),
        ]);
    }

    /**
     * Indicate that the quote is for motorcycle delivery.
     */
    public function forMotorcycle(): static
    {
        return $this->state(fn (array $attributes) => [
            'vehicle_type' => 'motorcycle',
            'cargo_weight' => $this->faker->randomFloat(2, 0.1, 2),
            'base_price' => $this->faker->numberBetween(100000, 1000000),
            'distance' => $this->faker->randomFloat(2, 5, 50),
        ]);
    }

    private function getTermsConditions(): string
    {
        return "1. Giá trên chưa bao gồm VAT 10%\n" .
               "2. Thời gian hiệu lực báo giá: 15 ngày kể từ ngày lập\n" .
               "3. Phương thức thanh toán: Chuyển khoản sau khi giao hàng\n" .
               "4. Công ty không chịu trách nhiệm với hàng hóa không được khai báo đầy đủ\n" .
               "5. Mọi thay đổi về lịch trình cần thông báo trước 24h";
    }
}