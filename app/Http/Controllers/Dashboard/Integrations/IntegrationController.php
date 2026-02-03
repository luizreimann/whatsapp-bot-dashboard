<?php

namespace App\Http\Controllers\Dashboard\Integrations;

use App\Integrations\IntegrationRegistry;
use App\Enums\IntegrationProvider;
use App\Models\IntegrationAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends \App\Http\Controllers\Controller
{
    /**
     * Lista integrações disponíveis + integrações conectadas do tenant.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;

        // Contas de integração já configuradas para este tenant
        $accounts = IntegrationAccount::where('tenant_id', $tenant->id)
            ->get()
            ->keyBy('provider');

        // Definições vindas do IntegrationRegistry (meta nas classes)
        $definitions = IntegrationRegistry::all();

        // Monta estrutura agrupada por categoria para a view
        $categories = $definitions
            ->groupBy('category') // 'crm', 'payment', etc.
            ->map(function ($providers) use ($accounts) {
                return $providers->map(function (array $def) use ($accounts) {
                    $account = $accounts->get($def['provider']);

                    return [
                        'provider'  => $def['provider'], // string, ex: 'rd_station_crm'
                        'meta'      => $def,             // label, description, icon, auth_type...
                        'account'   => $account,
                        'connected' => (bool) optional($account)->isConnected(),
                        'status'    => $account->status ?? null,
                    ];
                });
            });

        return view('dashboard.integrations.index', [
            'categories' => $categories, // << nome esperado pela Blade nova
        ]);
    }

    /**
     * Exibe o formulário de conexão/edição de uma integração específica.
     */
    public function showConnectForm(string $provider)
    {
        $tenant = Auth::user()->tenant;

        $providerEnum = IntegrationProvider::from($provider);

        $account = IntegrationAccount::where('tenant_id', $tenant->id)
            ->where('provider', $providerEnum->value)
            ->first();

        // Busca metadados da integração no Registry
        $meta = IntegrationRegistry::definition($providerEnum);

        if (! $meta) {
            abort(404);
        }

        return view('dashboard.integrations.connect', [
            'provider' => $providerEnum,
            'account'  => $account,
            'meta'     => $meta, // << importante para a Blade
        ]);
    }

    /**
     * Salva/atualiza a conta de integração para o tenant.
     * Aqui deixamos genérico; validação específica por provider entra depois.
     */
    public function connect(Request $request, string $provider)
    {
        $tenant = Auth::user()->tenant;
        $providerEnum = IntegrationProvider::from($provider);

        // Validação genérica - depois você especializa por provider
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            // credenciais específicas podem ser validadas depois
        ]);

        // Tudo que não é _token ou name vai para o config (tokens, keys, ids, etc.)
        $config = $request->except(['_token', 'name', '_method']);

        $account = IntegrationAccount::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'provider'  => $providerEnum->value,
            ],
            [
                'category'      => $providerEnum->category()->value,
                'name'          => $data['name'] ?? $providerEnum->label(),
                'config'        => $config,
                'status'        => 'connected',
                'connected_at'  => now(),
            ]
        );

        // Aqui mais pra frente você pode chamar um service e testar conexão:
        // app(YourService::class)->testConnection($account);

        return redirect()
            ->route('dashboard.integrations.index')
            ->with('success', 'Integração conectada/atualizada com sucesso.');
    }

    /**
     * Desconecta/remover uma conta de integração.
     */
    public function disconnect(IntegrationAccount $integrationAccount)
    {
        $tenant = Auth::user()->tenant;

        // Garantir que o tenant não remova conta de outro tenant
        if ($integrationAccount->tenant_id !== $tenant->id) {
            abort(403);
        }

        $integrationAccount->delete();

        return redirect()
            ->route('dashboard.integrations.index')
            ->with('success', 'Integração desconectada com sucesso.');
    }
}