<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subscription;
use App\Models\Tenant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => \App\Models\Subscription::factory(),
            'tenant_id' => \App\Models\Tenant::factory(),
            'amount' => 297.00,
            'currency' => 'BRL',
            'status' => 'paid',
            'payment_method' => 'stripe',
            'external_payment_id' => 'pi_' . fake()->uuid(),
            'payment_link' => fake()->url(),
            'metadata' => [
                'stripe_id' => 'ch_' . fake()->uuid(),
                'customer' => 'cus_' . fake()->uuid(),
            ],
            'paid_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
            'failed_at' => now(),
        ]);
    }
}
