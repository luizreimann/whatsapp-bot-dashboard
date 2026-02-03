<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Services\Payment\PaymentService;
use App\Services\Payment\TenantProvisioningService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected TenantProvisioningService $provisioningService
    ) {}

    public function index(Request $request)
    {
        $query = Tenant::with(['subscription', 'users']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['subscription.payments', 'users', 'leads', 'fluxes', 'whatsappInstance']);

        return view('admin.tenants.show', compact('tenant'));
    }

    public function suspend(Tenant $tenant)
    {
        $this->provisioningService->suspend($tenant);

        return back()->with('success', 'Tenant suspenso com sucesso.');
    }

    public function reactivate(Tenant $tenant)
    {
        $this->provisioningService->reactivate($tenant);

        if ($tenant->subscription) {
            $tenant->subscription->update([
                'status' => 'active',
                'suspended_at' => null,
            ]);
        }

        return back()->with('success', 'Tenant reativado com sucesso.');
    }

    public function generatePaymentLink(Tenant $tenant)
    {
        $subscription = $tenant->subscription;

        if (!$subscription) {
            return back()->withErrors(['error' => 'Tenant não possui assinatura.']);
        }

        $result = $this->paymentService->createPaymentLink($subscription);

        if (!$result['success']) {
            return back()->withErrors(['error' => 'Erro ao gerar link de pagamento.']);
        }

        return back()->with('success', 'Link de pagamento gerado com sucesso!')
            ->with('payment_link', $result['url']);
    }
}
