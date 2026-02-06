<?php

namespace Tests\Feature\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Step 1 ───────────────────────────────────────────

    public function test_step1_shows_form(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register.step1');
    }

    public function test_step1_validates_required_fields(): void
    {
        $response = $this->post(route('register.step1'), []);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_step1_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register.step1'), [
            'name'                  => 'John Doe',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_step1_validates_password_confirmation(): void
    {
        $response = $this->post(route('register.step1'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_step1_validates_cpf_when_provided(): void
    {
        $response = $this->post(route('register.step1'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'document'              => '00000000000',
        ]);

        $response->assertSessionHasErrors(['document']);
    }

    public function test_step1_saves_data_to_session(): void
    {
        $response = $this->post(route('register.step1'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'phone'                 => '(11) 99999-9999',
        ]);

        $response->assertRedirect(route('register.company'));
        $response->assertSessionHas('onboarding');

        $onboarding = session('onboarding');
        $this->assertEquals('John Doe', $onboarding['name']);
        $this->assertEquals('john@example.com', $onboarding['email']);
        $this->assertEquals('11999999999', $onboarding['phone']);
    }

    // ─── Step 2 ───────────────────────────────────────────

    public function test_step2_redirects_if_session_empty(): void
    {
        $response = $this->get(route('register.company'));
        $response->assertRedirect(route('register'));
    }

    public function test_step2_shows_form_with_valid_session(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'hashed',
        ]]);

        $response = $this->get(route('register.company'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register.step2');
    }

    public function test_step2_creates_tenant_user_subscription_without_company(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
            'phone'    => '11999999999',
        ]]);

        $response = $this->post(route('register.step2'), []);

        $this->assertDatabaseHas('tenants', [
            'name'   => 'John Doe',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '11999999999',
            'role'  => 'admin',
        ]);

        $tenant = Tenant::where('name', 'John Doe')->first();
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'status'    => 'pending',
            'amount'    => 297.00,
        ]);

        $this->assertDatabaseMissing('companies', [
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_step2_creates_company_when_data_provided(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
        ]]);

        $response = $this->post(route('register.step2'), [
            'company_name'  => 'Acme Corp',
            'cnpj'          => '11.222.333/0001-81',
            'company_phone' => '(11) 3333-4444',
            'company_email' => 'contato@acme.com',
            'segment'       => 'tecnologia',
            'zip'           => '01001-000',
            'street'        => 'Rua das Flores',
            'number'        => '123',
            'neighborhood'  => 'Centro',
            'city'          => 'São Paulo',
            'state'         => 'SP',
        ]);

        $tenant = Tenant::where('name', 'John Doe')->first();

        $this->assertDatabaseHas('companies', [
            'tenant_id' => $tenant->id,
            'name'      => 'Acme Corp',
            'document'  => '11222333000181',
            'phone'     => '1133334444',
            'email'     => 'contato@acme.com',
            'segment'   => 'tecnologia',
        ]);
    }

    public function test_step2_validates_cnpj_when_provided(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
        ]]);

        $response = $this->post(route('register.step2'), [
            'company_name' => 'Acme Corp',
            'cnpj'         => '00000000000000',
        ]);

        $response->assertSessionHasErrors(['cnpj']);
    }

    public function test_step2_logs_user_in_and_redirects_to_checkout(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
        ]]);

        $response = $this->post(route('register.step2'), []);

        $this->assertAuthenticated();
        $this->assertEquals('john@example.com', auth()->user()->email);

        $subscription = Subscription::latest()->first();
        $response->assertRedirect(route('checkout.index', $subscription));
    }

    public function test_step2_creates_unique_tenant_slug(): void
    {
        $this->withSession(['onboarding' => [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
        ]]);

        $this->post(route('register.step2'), []);

        $tenant = Tenant::where('name', 'John Doe')->first();
        $this->assertStringStartsWith('john-doe-', $tenant->slug);
    }
}
