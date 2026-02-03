<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_belongs_to_subscription(): void
    {
        $subscription = Subscription::factory()->create();
        $payment = Payment::factory()->create(['subscription_id' => $subscription->id]);

        $this->assertInstanceOf(Subscription::class, $payment->subscription);
        $this->assertEquals($subscription->id, $payment->subscription->id);
    }

    public function test_payment_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $payment = Payment::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $payment->tenant);
        $this->assertEquals($tenant->id, $payment->tenant->id);
    }

    public function test_is_paid_returns_true_for_paid_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'paid']);

        $this->assertTrue($payment->isPaid());
    }

    public function test_is_paid_returns_false_for_pending_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $this->assertFalse($payment->isPaid());
    }

    public function test_is_pending_returns_true_for_pending_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $this->assertTrue($payment->isPending());
    }

    public function test_is_failed_returns_true_for_failed_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'failed']);

        $this->assertTrue($payment->isFailed());
    }

    public function test_scope_paid_filters_paid_payments(): void
    {
        Payment::factory()->create(['status' => 'paid']);
        Payment::factory()->create(['status' => 'pending']);
        Payment::factory()->create(['status' => 'paid']);

        $paidPayments = Payment::paid()->get();

        $this->assertCount(2, $paidPayments);
    }

    public function test_scope_pending_filters_pending_payments(): void
    {
        Payment::factory()->create(['status' => 'pending']);
        Payment::factory()->create(['status' => 'paid']);
        Payment::factory()->create(['status' => 'pending']);

        $pendingPayments = Payment::pending()->get();

        $this->assertCount(2, $pendingPayments);
    }

    public function test_scope_failed_filters_failed_payments(): void
    {
        Payment::factory()->create(['status' => 'failed']);
        Payment::factory()->create(['status' => 'paid']);

        $failedPayments = Payment::failed()->get();

        $this->assertCount(1, $failedPayments);
    }

    public function test_metadata_is_cast_to_array(): void
    {
        $metadata = ['stripe_id' => 'ch_123', 'customer' => 'cus_456'];
        $payment = Payment::factory()->create(['metadata' => $metadata]);

        $this->assertIsArray($payment->metadata);
        $this->assertEquals($metadata, $payment->metadata);
    }

    public function test_amount_is_cast_to_decimal(): void
    {
        $payment = Payment::factory()->create(['amount' => 297.50]);

        $this->assertEquals('297.50', $payment->amount);
    }

    public function test_timestamps_are_cast_correctly(): void
    {
        $payment = Payment::factory()->create([
            'paid_at' => now(),
            'failed_at' => null,
            'refunded_at' => null,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $payment->paid_at);
        $this->assertNull($payment->failed_at);
        $this->assertNull($payment->refunded_at);
    }
}
