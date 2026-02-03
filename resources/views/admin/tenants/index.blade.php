@extends('layouts.app')

@section('title', 'Gerenciar Tenants | Admin')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Gerenciar Tenants</h2>
    <p class="text-muted">Visualize e gerencie todos os tenants do sistema</p>
</div>

<!-- Filtros -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.tenants.index') }}" class="row g-2">
            <div class="col-md-6">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar por nome..."
                    class="form-control"
                >
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Tenants -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tenant</th>
                    <th>Status</th>
                    <th>Assinatura</th>
                    <th>Usuários</th>
                    <th>Criado em</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td>
                            <div>
                                <div class="fw-medium">{{ $tenant->name }}</div>
                                <div class="text-muted small">{{ $tenant->slug }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="badge 
                                @if($tenant->status === 'active') bg-success
                                @elseif($tenant->status === 'pending') bg-warning
                                @else bg-danger
                                @endif">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td>
                            @if($tenant->subscription)
                                <div>
                                    <div class="fw-medium">R$ {{ number_format($tenant->subscription->amount, 2, ',', '.') }}/mês</div>
                                    <div class="text-muted small">{{ ucfirst($tenant->subscription->status) }}</div>
                                </div>
                            @else
                                <span class="text-muted small">Sem assinatura</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $tenant->users->count() }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $tenant->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($tenant->status === 'active')
                                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja suspender este tenant?')">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                </form>
                            @elseif($tenant->status === 'suspended')
                                <form method="POST" action="{{ route('admin.tenants.reactivate', $tenant) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Nenhum tenant encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
@if($tenants->hasPages())
    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
@endif
@endsection
