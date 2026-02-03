<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CheckExpiredSubscriptions;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckExpiredSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_suspends_expired_subscriptions(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $subscription = Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'active',
            'current_period_end' => now()->subDays(10),
        ]);

        $job = new CheckExpiredSubscriptions();
        $job->handle();

        $this->assertEquals('suspended', $subscription->fresh()->status);
        $this->assertNotNull($subscription->fresh()->suspended_at);
        $this->assertEquals('suspended', $tenant->fresh()->status);
    }

    public function test_job_does_not_suspend_valid_subscriptions(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $subscription = Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'active',
            'current_period_end' => now()->addDays(10),
        ]);

        $job = new CheckExpiredSubscriptions();
        $job->handle();

        $this->assertEquals('active', $subscription->fresh()->status);
        $this->assertEquals('active', $tenant->fresh()->status);
    }

    public function test_job_only_checks_active_subscriptions(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => 'pending',
            'current_period_end' => now()->subDays(10),
        ]);

        $job = new CheckExpiredSubscriptions();
        $job->handle();

        $this->assertEquals('pending', $subscription->fresh()->status);
    }

    public function test_job_respects_grace_period(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $subscription = Subscription::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'active',
            'current_period_end' => now()->subDays(5),
        ]);

        $job = new CheckExpiredSubscriptions();
        $job->handle();

        $this->assertEquals('active', $subscription->fresh()->status);
    }
}
