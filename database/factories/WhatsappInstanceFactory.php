<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatsappInstance>
 */
class WhatsappInstanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => \App\Models\Tenant::factory(),
            'status' => 'inactive',
            'bot_token' => \Illuminate\Support\Str::random(40),
            'number' => null,
            'fly_app_name' => null,
            'public_url' => null,
            'last_status_payload' => null,
            'last_connected_at' => null,
        ];
    }
}
