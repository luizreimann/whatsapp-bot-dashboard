<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;
use Exception;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentLink(Subscription $subscription, array $options = []): array
    {
        try {
            $tenant = $subscription->tenant;
            $user = $tenant->users()->first();

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($subscription->currency),
                        'product_data' => [
                            'name' => 'Zaptria - Assinatura Mensal',
                            'description' => 'Automação de WhatsApp com Flow Builder',
                        ],
                        'unit_amount' => (int) ($subscription->amount * 100),
                        'recurring' => [
                            'interval' => 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $options['success_url'] ?? route('checkout.success'),
                'cancel_url' => $options['cancel_url'] ?? route('checkout.index', $subscription),
                'customer_email' => $user->email,
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'subscription_id' => $subscription->id,
                ],
            ];

            $session = StripeSession::create($sessionData);

            Payment::create([
                'subscription_id' => $subscription->id,
                'tenant_id' => $tenant->id,
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
                'status' => 'pending',
                'payment_method' => 'stripe',
                'external_payment_id' => $session->id,
                'payment_link' => $session->url,
            ]);

            Log::info('Payment link created', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'session_id' => $session->id,
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::error('Failed to create payment link', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            Log::info('Stripe webhook received', [
                'type' => $event->type,
                'id' => $event->id,
            ]);

            switch ($event->type) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutCompleted($event->data->object);
                
                case 'invoice.payment_succeeded':
                    return $this->handleInvoicePaymentSucceeded($event->data->object);
                
                case 'invoice.payment_failed':
                    return $this->handleInvoicePaymentFailed($event->data->object);
                
                case 'customer.subscription.deleted':
                    return $this->handleSubscriptionDeleted($event->data->object);
                
                default:
                    Log::info('Unhandled webhook event type', ['type' => $event->type]);
                    return ['success' => true, 'message' => 'Event type not handled'];
            }
        } catch (Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function handleCheckoutCompleted($session): array
    {
        $payment = Payment::where('external_payment_id', $session->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for session', ['session_id' => $session->id]);
            return ['success' => false, 'error' => 'Payment not found'];
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'metadata' => $session,
        ]);

        $subscription = $payment->subscription;
        $subscription->update([
            'status' => 'active',
            'payment_method' => 'stripe',
            'external_subscription_id' => $session->subscription,
            'external_customer_id' => $session->customer,
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $tenant = $subscription->tenant;
        $tenant->update(['status' => 'active']);

        app(TenantProvisioningService::class)->provision($tenant);

        Log::info('Checkout completed successfully', [
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
        ]);

        return ['success' => true, 'message' => 'Checkout completed'];
    }

    protected function handleInvoicePaymentSucceeded($invoice): array
    {
        $subscriptionId = $invoice->subscription;
        $subscription = Subscription::where('external_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for invoice', ['subscription_id' => $subscriptionId]);
            return ['success' => false, 'error' => 'Subscription not found'];
        }

        Payment::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'amount' => $invoice->amount_paid / 100,
            'currency' => strtoupper($invoice->currency),
            'status' => 'paid',
            'payment_method' => 'stripe',
            'external_payment_id' => $invoice->id,
            'paid_at' => now(),
            'metadata' => $invoice,
        ]);

        $subscription->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        if ($subscription->tenant->status === 'suspended') {
            app(TenantProvisioningService::class)->reactivate($subscription->tenant);
        }

        Log::info('Invoice payment succeeded', [
            'tenant_id' => $subscription->tenant_id,
            'invoice_id' => $invoice->id,
        ]);

        return ['success' => true, 'message' => 'Invoice payment processed'];
    }

    protected function handleInvoicePaymentFailed($invoice): array
    {
        $subscriptionId = $invoice->subscription;
        $subscription = Subscription::where('external_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            return ['success' => false, 'error' => 'Subscription not found'];
        }

        Payment::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'amount' => $invoice->amount_due / 100,
            'currency' => strtoupper($invoice->currency),
            'status' => 'failed',
            'payment_method' => 'stripe',
            'external_payment_id' => $invoice->id,
            'failed_at' => now(),
            'metadata' => $invoice,
        ]);

        $subscription->update(['status' => 'past_due']);

        Log::warning('Invoice payment failed', [
            'tenant_id' => $subscription->tenant_id,
            'invoice_id' => $invoice->id,
        ]);

        return ['success' => true, 'message' => 'Invoice payment failure recorded'];
    }

    protected function handleSubscriptionDeleted($stripeSubscription): array
    {
        $subscription = Subscription::where('external_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            return ['success' => false, 'error' => 'Subscription not found'];
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        app(TenantProvisioningService::class)->suspend($subscription->tenant);

        Log::info('Subscription canceled', [
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
        ]);

        return ['success' => true, 'message' => 'Subscription canceled'];
    }

    public function cancelSubscription(Subscription $subscription): array
    {
        try {
            if ($subscription->external_subscription_id) {
                $stripeSubscription = \Stripe\Subscription::retrieve($subscription->external_subscription_id);
                $stripeSubscription->cancel();
            }

            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            app(TenantProvisioningService::class)->suspend($subscription->tenant);

            Log::info('Subscription canceled manually', [
                'tenant_id' => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
            ]);

            return ['success' => true, 'message' => 'Subscription canceled successfully'];
        } catch (Exception $e) {
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
