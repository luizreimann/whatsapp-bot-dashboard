<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Flux;
use App\Enums\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    protected function buildQuery(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        // Ordenação
        $sort      = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $sortMap = [
            'flux'       => 'flux_id',
            'status'     => 'status',
            'created_at' => 'created_at',
        ];

        $column = $sortMap[$sort] ?? 'created_at';

        $query = Lead::with('flux')
            ->where('tenant_id', $tenantId)
            ->orderBy($column, $direction);

        // Fluxos
        $fluxIds = $request->input('flux', []);
        if (is_array($fluxIds) && count($fluxIds) > 0) {
            $query->whereIn('flux_id', $fluxIds);
        }

        // Status
        $statuses = $request->input('status', []);
        if (is_array($statuses) && count($statuses) > 0) {
            $query->whereIn('status', $statuses);
        }

        // Intervalo de datas
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [$query, $sort, $direction];
    }

    public function index(Request $request)
    {
        [$query, $sort, $direction] = $this->buildQuery($request);

        $leads = $query
            ->paginate(15)
            ->appends($request->query());

        $tenantId = Auth::user()->tenant_id;

        // Preencher os filtros no front
        $allFluxes = Flux::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $allStatuses = LeadStatus::cases();

        return view('dashboard.leads.index', [
            'leads'      => $leads,
            'sort'       => $sort,
            'direction'  => $direction,
            'allFluxes'  => $allFluxes,
            'allStatuses'=> $allStatuses,
            'filters'    => [
                'flux'      => $request->input('flux', []),
                'status'    => $request->input('status', []),
                'date_from' => $request->input('date_from'),
                'date_to'   => $request->input('date_to'),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $sort, $direction] = $this->buildQuery($request);

        $leads = $query->paginate(15)->appends([
            'sort'      => $sort,
            'direction' => $direction,
        ]);

        $html = view('dashboard.leads.partials.table', compact('leads', 'sort', 'direction'))->render();

        return response()->json([
            'html'      => $html,
            'sort'      => $sort,
            'direction' => $direction,
        ]);
    }

    public function show(Lead $lead)
    {
        $user = auth()->user();

        if ($lead->tenant_id !== $user->tenant_id) {
            abort(404);
        }

        return view('dashboard.leads.show', [
            'lead' => $lead,
        ]);
    }

    public function updateNotes(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if ($lead->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data = $lead->data ?? [];
        $data['notes'] = $validated['notes'] ?? null;

        $lead->data = $data;
        $lead->save();

        return response()->json([
            'success' => true,
            'notes'   => $data['notes'] ?? '',
        ]);
    }
}