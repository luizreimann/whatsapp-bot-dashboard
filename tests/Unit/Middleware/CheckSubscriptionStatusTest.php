<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\CheckSubscriptionStatus;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CheckSubscriptionStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_access_with_active_subscription(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'active',
        ]);

        $this->assertTrue($user->tenant->subscription->isActive());
    }

    public function test_redirects_without_subscription(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $request = Request::create('/dashboard', 'GET');
        $middleware = new CheckSubscriptionStatus();

        $this->actingAs($user);

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_redirects_with_inactive_subscription(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'suspended',
        ]);

        $request = Request::create('/dashboard', 'GET');
        $middleware = new CheckSubscriptionStatus();

        $this->actingAs($user);

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $request = Request::create('/dashboard', 'GET');
        $middleware = new CheckSubscriptionStatus();

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }
}
