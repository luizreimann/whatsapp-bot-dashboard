<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index()
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription;

        if (!$subscription) {
            return view('dashboard.subscription', [
                'subscription' => null,
                'payments' => collect([]),
            ]);
        }

        $payments = $subscription->payments()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.subscription', compact('subscription', 'payments'));
    }

    public function cancel()
    {
        $subscription = auth()->user()->tenant->subscription;

        if (!$subscription) {
            return back()->withErrors(['error' => 'Você não possui uma assinatura ativa.']);
        }

        $result = $this->paymentService->cancelSubscription($subscription);

        if (!$result['success']) {
            return back()->withErrors(['error' => 'Erro ao cancelar assinatura. Tente novamente.']);
        }

        return redirect()->route('dashboard.subscription')
            ->with('success', 'Assinatura cancelada com sucesso.');
    }
}
