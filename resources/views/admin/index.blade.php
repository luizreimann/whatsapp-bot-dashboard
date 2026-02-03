@extends('layouts.app')

@section('title', 'Painel Admin | Zaptria')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Painel Administrativo</h2>
    <p class="text-muted">Visão geral do sistema</p>
</div>

<!-- Métricas Principais -->
<div class="row g-3 mb-4">
    <!-- Total de Tenants -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total de Tenants</p>
                        <h3 class="mb-0">{{ $totalTenants }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-2">
                        <i class="fa-solid fa-users text-primary fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    <span class="text-success">{{ $activeTenants }} ativos</span>
                    <span class="text-muted mx-1">•</span>
                    <span class="text-warning">{{ $pendingTenants }} pendentes</span>
                    <span class="text-muted mx-1">•</span>
                    <span class="text-danger">{{ $suspendedTenants }} suspensos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Assinaturas Ativas -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Assinaturas Ativas</p>
                        <h3 class="mb-0">{{ $activeSubscriptions }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-2">
                        <i class="fa-solid fa-circle-check text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MRR -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">MRR</p>
                        <h3 class="mb-0">R$ {{ number_format($mrr, 2, ',', '.') }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-2">
                        <i class="fa-solid fa-dollar-sign text-info fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mb-0 mt-2">Receita Recorrente Mensal</p>
            </div>
        </div>
    </div>

    <!-- Expirando em Breve -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Expirando em 7 dias</p>
                        <h3 class="mb-0">{{ $expiringSoon->count() }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-2">
                        <i class="fa-solid fa-clock text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pagamentos Recentes -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Pagamentos Recentes</h5>
            </div>
            <div class="card-body">
                @forelse($recentPayments as $payment)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-medium">{{ $payment->tenant->name }}</p>
                            <p class="text-muted small mb-0">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1 fw-semibold">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                            <span class="badge 
                                @if($payment->status === 'paid') bg-success
                                @elseif($payment->status === 'pending') bg-warning
                                @else bg-danger
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Nenhum pagamento registrado</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tenants Recentes -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Tenants Recentes</h5>
            </div>
            <div class="card-body">
                @forelse($recentTenants as $tenant)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-medium">{{ $tenant->name }}</p>
                            <p class="text-muted small mb-0">{{ $tenant->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="badge 
                                @if($tenant->status === 'active') bg-success
                                @elseif($tenant->status === 'pending') bg-warning
                                @else bg-danger
                                @endif">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Nenhum tenant cadastrado</p>
                @endforelse
                
                @if($recentTenants->isNotEmpty())
                    <div class="mt-3">
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-primary btn-sm w-100">
                            Ver todos os tenants <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
