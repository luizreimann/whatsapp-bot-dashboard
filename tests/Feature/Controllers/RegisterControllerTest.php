<?php

namespace Tests\Feature\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_tenant_user_and_subscription(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
        ];

        $response = $this->post(route('register.post'), $data);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Corp',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
            'is_admin' => false,
        ]);

        $tenant = Tenant::where('name', 'Acme Corp')->first();
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'status' => 'pending',
            'amount' => 297.00,
            'currency' => 'BRL',
        ]);
    }

    public function test_registration_redirects_to_checkout(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
        ];

        $response = $this->post(route('register.post'), $data);

        $subscription = Subscription::latest()->first();
        $response->assertRedirect(route('checkout.index', $subscription));
    }

    public function test_registration_logs_user_in(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
        ];

        $response = $this->post(route('register.post'), $data);

        $this->assertAuthenticated();
        $this->assertEquals('john@example.com', auth()->user()->email);
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post(route('register.post'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'company_name']);
    }

    public function test_registration_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
        ];

        $response = $this->post(route('register.post'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_validates_password_confirmation(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'company_name' => 'Acme Corp',
        ];

        $response = $this->post(route('register.post'), $data);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_creates_unique_tenant_slug(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
        ];

        $this->post(route('register.post'), $data);

        $tenant = Tenant::where('name', 'Acme Corp')->first();
        $this->assertStringStartsWith('acme-corp-', $tenant->slug);
        $this->assertNotEquals('acme-corp', $tenant->slug);
    }
}
