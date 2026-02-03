<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Subscription $subscription)
    {
        if ($subscription->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($subscription->isActive()) {
            return redirect()->route('dashboard.home')
                ->with('info', 'Sua assinatura já está ativa.');
        }

        // Validar configuração do Stripe
        $stripeSecret = config('services.stripe.secret');
        $stripeKey = config('services.stripe.key');

        if (empty($stripeSecret) || empty($stripeKey)) {
            return back()->withErrors([
                'error' => 'Configuração do Stripe não encontrada. Por favor, configure as chaves STRIPE_KEY e STRIPE_SECRET no arquivo .env'
            ]);
        }

        try {
            // Criar Setup Intent para capturar método de pagamento
            $stripe = new \Stripe\StripeClient($stripeSecret);
            
            $setupIntent = $stripe->setupIntents->create([
                'payment_method_types' => ['card'],
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->tenant_id,
                ],
            ]);

            return view('checkout.index', [
                'subscription' => $subscription,
                'clientSecret' => $setupIntent->client_secret,
                'stripeKey' => $stripeKey,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Erro ao inicializar checkout: ' . $e->getMessage()
            ]);
        }
    }

    public function updateAmount(Request $request, Subscription $subscription)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:9999.99',
        ]);

        $subscription->update([
            'amount' => $validated['amount'],
        ]);

        return back()->with('success', 'Valor atualizado com sucesso!');
    }

    public function createPayment(Subscription $subscription)
    {
        if ($subscription->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $result = $this->paymentService->createPaymentLink($subscription, [
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.index', $subscription),
        ]);

        if (!$result['success']) {
            return back()->withErrors(['error' => 'Erro ao gerar link de pagamento. Tente novamente.']);
        }

        return redirect($result['url']);
    }

    public function processPayment(Request $request, Subscription $subscription)
    {
        if ($subscription->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            // Criar ou recuperar cliente Stripe
            $customer = $stripe->customers->create([
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
                'metadata' => [
                    'tenant_id' => $subscription->tenant_id,
                    'user_id' => auth()->user()->id,
                ],
            ]);

            // Anexar método de pagamento ao cliente
            $stripe->paymentMethods->attach(
                $validated['payment_method_id'],
                ['customer' => $customer->id]
            );

            // Definir como método padrão
            $stripe->customers->update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $validated['payment_method_id'],
                ],
            ]);

            // Primeiro, criar um produto
            $product = $stripe->products->create([
                'name' => 'Plano Mensal Zaptria',
                'description' => 'Automação completa de WhatsApp',
            ]);

            // Depois, criar um preço para o produto
            $price = $stripe->prices->create([
                'product' => $product->id,
                'unit_amount' => (int)($subscription->amount * 100), // Converter para centavos
                'currency' => strtolower($subscription->currency),
                'recurring' => [
                    'interval' => 'month',
                ],
            ]);

            // Criar assinatura no Stripe
            $stripeSubscription = $stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $price->id],
                ],
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->tenant_id,
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Atualizar subscription local
            $subscription->update([
                'status' => $stripeSubscription->status === 'active' ? 'active' : 'pending',
                'external_subscription_id' => $stripeSubscription->id,
                'external_customer_id' => $customer->id,
                'payment_method' => 'card',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Atualizar tenant
            $subscription->tenant->update(['status' => 'active']);

            // Provisionar tenant
            app(\App\Services\Payment\TenantProvisioningService::class)->provision($subscription->tenant);

            return response()->json([
                'success' => true,
                'redirect' => route('checkout.success'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function success()
    {
        return view('checkout.success');
    }
}
