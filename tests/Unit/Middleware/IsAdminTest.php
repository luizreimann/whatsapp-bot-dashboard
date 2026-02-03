<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\IsAdmin;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class IsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_access_for_admin_user(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_admin' => true,
        ]);

        $this->assertTrue($admin->isAdmin());
    }

    public function test_denies_access_for_non_admin_user(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_admin' => false,
        ]);

        $this->assertFalse($user->isAdmin());
    }

    public function test_admin_scope_filters_correctly(): void
    {
        User::factory()->create(['is_admin' => true]);
        User::factory()->create(['is_admin' => false]);
        User::factory()->create(['is_admin' => true]);

        $admins = User::admins()->get();

        $this->assertCount(2, $admins);
    }
}
