<?php

namespace Database\Factories;

use App\Models\SalaryAdvanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryAdvanceRequest>
 */
class SalaryAdvanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'request_code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{8}'),
            'request_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'amount' => $this->faker->randomFloat(2, 100000, 10000000),
            'advance_month' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'reason' => $this->faker->sentence(),
            'status' => $this->faker->randomElement([
                SalaryAdvanceRequest::STATUS_DRAFT,
                SalaryAdvanceRequest::STATUS_PENDING,
                SalaryAdvanceRequest::STATUS_APPROVED,
                SalaryAdvanceRequest::STATUS_REJECTED,
                SalaryAdvanceRequest::STATUS_PAID,
                SalaryAdvanceRequest::STATUS_DEDUCTED,
                SalaryAdvanceRequest::STATUS_CANCELLED
            ]),
            'type' => $this->faker->randomElement([
                SalaryAdvanceRequest::TYPE_SALARY,
                SalaryAdvanceRequest::TYPE_BONUS,
                SalaryAdvanceRequest::TYPE_PENALTY,
                SalaryAdvanceRequest::TYPE_PAYMENT,
                SalaryAdvanceRequest::TYPE_OTHER
            ]),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    /**
     * Indicate that the request is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceRequest::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate that the request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceRequest::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceRequest::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the request is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceRequest::STATUS_PAID,
        ]);
    }

    /**
     * Indicate that the request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceRequest::STATUS_REJECTED,
        ]);
    }

    /**
     * Indicate that the request is for salary advance.
     */
    public function salary(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryAdvanceRequest::TYPE_SALARY,
        ]);
    }

    /**
     * Indicate that the request is for bonus.
     */
    public function bonus(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryAdvanceRequest::TYPE_BONUS,
        ]);
    }

    /**
     * Indicate that the request is for penalty.
     */
    public function penalty(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryAdvanceRequest::TYPE_PENALTY,
        ]);
    }

    /**
     * Indicate that the request is for payment.
     */
    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryAdvanceRequest::TYPE_PAYMENT,
        ]);
    }
}
