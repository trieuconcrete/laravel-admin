<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement(['individual', 'business']);
        $isIndividual = $type === 'individual';
        
        $name = $isIndividual 
                ? $this->faker->name() 
                : $this->faker->company();
                
        $prefix = $isIndividual ? 'IND' : 'BUS';
        $code = $prefix . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return [
            'customer_code' => $code,
            'name' => $name,
            'type' => $type,
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->streetAddress(),
            'province' => $this->faker->city(),
            'district' => $this->faker->citySuffix(),
            'ward' => 'Phường ' . $this->faker->numberBetween(1, 20),
            'tax_code' => $isIndividual ? null : $this->faker->numberBetween(1000000000, 9999999999),
            'establishment_date' => $isIndividual 
                                    ? $this->faker->dateTimeBetween('-60 years', '-18 years') 
                                    : $this->faker->dateTimeBetween('-30 years', '-1 years'),
            'website' => $isIndividual ? null : 'https://www.' . Str::slug($name) . '.com',
            'primary_contact_name' => $isIndividual ? null : $this->faker->name(),
            'primary_contact_phone' => $isIndividual ? null : $this->faker->phoneNumber(),
            'primary_contact_email' => $isIndividual ? null : $this->faker->safeEmail(),
            'primary_contact_position' => $isIndividual ? null : $this->faker->jobTitle(),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'is_active' => $this->faker->boolean(80),
            'created_by' => 1,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Cấu hình để tạo khách hàng cá nhân
     */
    public function individual()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'individual',
                'customer_code' => 'IND' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
                'name' => $this->faker->name(),
                'tax_code' => null,
                'website' => null,
                'primary_contact_name' => null,
                'primary_contact_phone' => null,
                'primary_contact_email' => null,
                'primary_contact_position' => null,
                'establishment_date' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            ];
        });
    }

    /**
     * Cấu hình để tạo khách hàng doanh nghiệp
     */
    public function business()
    {
        return $this->state(function (array $attributes) {
            $name = $this->faker->company();
            return [
                'type' => 'business',
                'customer_code' => 'BUS' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'tax_code' => $this->faker->numberBetween(1000000000, 9999999999),
                'website' => 'https://www.' . Str::slug($name) . '.com',
                'primary_contact_name' => $this->faker->name(),
                'primary_contact_phone' => $this->faker->phoneNumber(),
                'primary_contact_email' => $this->faker->safeEmail(),
                'primary_contact_position' => $this->faker->jobTitle(),
                'establishment_date' => $this->faker->dateTimeBetween('-30 years', '-1 years'),
            ];
        });
    }
}