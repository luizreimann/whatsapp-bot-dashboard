<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\Payment\TenantProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('current_period_end', '<', now()->subDays(7))
            ->get();

        Log::info('Checking expired subscriptions', [
            'count' => $expiredSubscriptions->count(),
        ]);

        foreach ($expiredSubscriptions as $subscription) {
            $this->suspendSubscription($subscription);
        }

        Log::info('Expired subscriptions check completed', [
            'suspended_count' => $expiredSubscriptions->count(),
        ]);
    }

    protected function suspendSubscription(Subscription $subscription): void
    {
        $tenant = $subscription->tenant;

        $subscription->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        app(TenantProvisioningService::class)->suspend($tenant);

        Log::warning('Tenant suspended due to expired subscription', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'subscription_id' => $subscription->id,
            'expired_at' => $subscription->current_period_end,
        ]);
    }
}
