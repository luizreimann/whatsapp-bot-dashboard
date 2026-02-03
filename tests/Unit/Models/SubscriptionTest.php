<?php

namespace Tests\Unit\Models;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $subscription = Subscription::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $subscription->tenant);
        $this->assertEquals($tenant->id, $subscription->tenant->id);
    }

    public function test_subscription_has_many_payments(): void
    {
        $subscription = Subscription::factory()->create();
        $payment1 = Payment::factory()->create(['subscription_id' => $subscription->id]);
        $payment2 = Payment::factory()->create(['subscription_id' => $subscription->id]);

        $this->assertCount(2, $subscription->payments);
        $this->assertInstanceOf(Payment::class, $subscription->payments->first());
    }

    public function test_is_active_returns_true_for_active_subscription(): void
    {
        $subscription = Subscription::factory()->create(['status' => 'active']);

        $this->assertTrue($subscription->isActive());
    }

    public function test_is_active_returns_false_for_pending_subscription(): void
    {
        $subscription = Subscription::factory()->create(['status' => 'pending']);

        $this->assertFalse($subscription->isActive());
    }

    public function test_is_pending_returns_true_for_pending_subscription(): void
    {
        $subscription = Subscription::factory()->create(['status' => 'pending']);

        $this->assertTrue($subscription->isPending());
    }

    public function test_is_suspended_returns_true_for_suspended_subscription(): void
    {
        $subscription = Subscription::factory()->create(['status' => 'suspended']);

        $this->assertTrue($subscription->isSuspended());
    }

    public function test_is_expired_returns_true_for_expired_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'current_period_end' => now()->subDays(10),
        ]);

        $this->assertTrue($subscription->isExpired());
    }

    public function test_is_expired_returns_false_for_valid_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => 'active',
            'current_period_end' => now()->addDays(10),
        ]);

        $this->assertFalse($subscription->isExpired());
    }

    public function test_scope_active_filters_active_subscriptions(): void
    {
        Subscription::factory()->create(['status' => 'active']);
        Subscription::factory()->create(['status' => 'pending']);
        Subscription::factory()->create(['status' => 'active']);

        $activeSubscriptions = Subscription::active()->get();

        $this->assertCount(2, $activeSubscriptions);
    }

    public function test_scope_pending_filters_pending_subscriptions(): void
    {
        Subscription::factory()->create(['status' => 'pending']);
        Subscription::factory()->create(['status' => 'active']);
        Subscription::factory()->create(['status' => 'pending']);

        $pendingSubscriptions = Subscription::pending()->get();

        $this->assertCount(2, $pendingSubscriptions);
    }

    public function test_scope_suspended_filters_suspended_subscriptions(): void
    {
        Subscription::factory()->create(['status' => 'suspended']);
        Subscription::factory()->create(['status' => 'active']);

        $suspendedSubscriptions = Subscription::suspended()->get();

        $this->assertCount(1, $suspendedSubscriptions);
    }

    public function test_scope_expired_filters_expired_subscriptions(): void
    {
        Subscription::factory()->create([
            'status' => 'active',
            'current_period_end' => now()->subDays(10),
        ]);
        Subscription::factory()->create([
            'status' => 'active',
            'current_period_end' => now()->addDays(10),
        ]);

        $expiredSubscriptions = Subscription::expired()->get();

        $this->assertCount(1, $expiredSubscriptions);
    }

    public function test_amount_is_cast_to_decimal(): void
    {
        $subscription = Subscription::factory()->create(['amount' => 297.50]);

        $this->assertEquals('297.50', $subscription->amount);
    }

    public function test_dates_are_cast_correctly(): void
    {
        $subscription = Subscription::factory()->create([
            'current_period_start' => '2026-01-01',
            'current_period_end' => '2026-02-01',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $subscription->current_period_start);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $subscription->current_period_end);
    }
}
