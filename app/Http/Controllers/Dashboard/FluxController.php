<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Flux;
use App\Models\IntegrationAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FluxController extends Controller
{
    public function index()
    {
        $fluxes = Flux::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.fluxes.index', compact('fluxes'));
    }

    public function create()
    {
        return view('dashboard.fluxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $flux = Flux::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $validated['name'],
            'status' => 'draft',
            'data' => [
                'nodes' => [
                    [
                        'id' => 'start-1',
                        'type' => 'start',
                        'position' => ['x' => 250, 'y' => 50],
                        'data' => ['label' => 'Início', 'trigger' => 'any', 'keyword' => ''],
                    ],
                ],
                'edges' => [],
                'version' => 1,
                'description' => $validated['description'] ?? '',
            ],
            'conversion_goal' => '',
        ]);

        return redirect()->route('dashboard.fluxes.edit', $flux)
            ->with('success', 'Fluxo criado com sucesso!');
    }

    public function edit(Flux $flux)
    {
        $this->authorize('view', $flux);

        $integrations = IntegrationAccount::where('tenant_id', Auth::user()->tenant_id)
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'provider' => $account->provider,
                    'category' => $account->category,
                    'status' => $account->status,
                ];
            });

        return view('flow-builder', [
            'flux' => $flux,
            'integrations' => $integrations,
        ]);
    }

    public function update(Request $request, Flux $flux)
    {
        $this->authorize('update', $flux);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'data' => 'sometimes|required|array',
            'data.nodes' => 'sometimes|required|array',
            'data.edges' => 'sometimes|required|array',
            'status' => 'sometimes|in:draft,active,inactive',
        ]);

        $flux->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fluxo salvo com sucesso!',
                'flux' => $flux,
            ]);
        }

        return redirect()->route('dashboard.fluxes.index')
            ->with('success', 'Fluxo atualizado com sucesso!');
    }

    public function destroy(Flux $flux)
    {
        $this->authorize('delete', $flux);

        $flux->delete();

        return redirect()->route('dashboard.fluxes.index')
            ->with('success', 'Fluxo excluído com sucesso!');
    }

    public function duplicate(Flux $flux)
    {
        $this->authorize('view', $flux);

        $newFlux = $flux->replicate();
        $newFlux->name = $flux->name . ' (Cópia)';
        $newFlux->status = 'draft';
        $newFlux->save();

        return redirect()->route('dashboard.fluxes.edit', $newFlux)
            ->with('success', 'Fluxo duplicado com sucesso!');
    }

    public function toggleStatus(Flux $flux)
    {
        $this->authorize('update', $flux);

        $newStatus = $flux->status === 'active' ? 'inactive' : 'active';
        
        if ($newStatus === 'active') {
            $data = $flux->data;
            $nodes = $data['nodes'] ?? [];
            $edges = $data['edges'] ?? [];

            $hasStart = collect($nodes)->contains('type', 'start');
            $hasEnd = collect($nodes)->contains('type', 'end');

            if (!$hasStart || !$hasEnd) {
                return back()->with('error', 'O fluxo precisa ter um nó de início e pelo menos um nó de fim para ser ativado.');
            }
        }

        $flux->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'ativado' : 'desativado';
        return back()->with('success', "Fluxo {$statusLabel} com sucesso!");
    }
}
