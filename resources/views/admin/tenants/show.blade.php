@extends('layouts.app')

@section('title', 'Detalhes do Tenant | Admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Voltar para lista
    </a>
    <h2 class="mb-1">{{ $tenant->name }}</h2>
    <p class="text-muted">Detalhes completos do tenant</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('payment_link'))
    <div class="alert alert-info alert-dismissible fade show">
        <p class="fw-bold mb-2">Link de pagamento gerado:</p>
        <a href="{{ session('payment_link') }}" target="_blank" class="text-break">
            {{ session('payment_link') }}
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <!-- Dados do Tenant -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Informações do Tenant</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Nome</small>
                        <p class="mb-0">{{ $tenant->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Slug</small>
                        <p class="mb-0"><code>{{ $tenant->slug }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">
                            <span class="badge 
                                @if($tenant->status === 'active') bg-success
                                @elseif($tenant->status === 'pending') bg-warning
                                @else bg-danger
                                @endif">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Criado em</small>
                        <p class="mb-0">{{ $tenant->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assinatura -->
        @if($tenant->subscription)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Assinatura</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge 
                                    @if($tenant->subscription->status === 'active') bg-success
                                    @elseif($tenant->subscription->status === 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($tenant->subscription->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Valor</small>
                            <p class="mb-0 fw-semibold">R$ {{ number_format($tenant->subscription->amount, 2, ',', '.') }}/mês</p>
                        </div>
                        @if($tenant->subscription->current_period_start)
                            <div class="col-md-6">
                                <small class="text-muted">Período Atual</small>
                                <p class="mb-0 small">
                                    {{ $tenant->subscription->current_period_start->format('d/m/Y') }} - 
                                    {{ $tenant->subscription->current_period_end->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <small class="text-muted">Método de Pagamento</small>
                            <p class="mb-0">{{ $tenant->subscription->payment_method ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('admin.tenants.payment-link', $tenant) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-link me-1"></i>
                            Gerar Link de Pagamento
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Usuários -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Usuários ({{ $tenant->users->count() }})</h5>
            </div>
            <div class="card-body">
                @foreach($tenant->users as $user)
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                        <div>
                            <p class="mb-1 fw-medium">{{ $user->name }}</p>
                            <p class="mb-0 text-muted small">{{ $user->email }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ ucfirst($user->role) }}</small>
                            @if($user->is_admin)
                                <span class="badge bg-primary ms-2">Admin</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Ações -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Ações</h5>
            </div>
            <div class="card-body">
                @if($tenant->status === 'active')
                    <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Deseja suspender este tenant?')">
                            <i class="fa-solid fa-ban me-1"></i>
                            Suspender Tenant
                        </button>
                    </form>
                @elseif($tenant->status === 'suspended')
                    <form method="POST" action="{{ route('admin.tenants.reactivate', $tenant) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-check me-1"></i>
                            Reativar Tenant
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Estatísticas</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Total de Leads</small>
                    <h4 class="mb-0">{{ $tenant->leads->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Fluxos Criados</small>
                    <h4 class="mb-0">{{ $tenant->fluxes->count() }}</h4>
                </div>
                <div>
                    <small class="text-muted">WhatsApp</small>
                    <p class="mb-0">
                        @if($tenant->whatsappInstance)
                            <span class="badge 
                                @if($tenant->whatsappInstance->status === 'connected') bg-success
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($tenant->whatsappInstance->status) }}
                            </span>
                        @else
                            <span class="text-muted small">Não configurado</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
