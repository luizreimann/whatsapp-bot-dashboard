@extends('layouts.app')

@section('title', 'Minha Assinatura | Zaptria')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Minha Assinatura</h2>
    <p class="text-muted">Gerencie sua assinatura e histórico de pagamentos</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($subscription)
    <!-- Card da Assinatura -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="mb-1">Plano Mensal</h4>
                    <p class="text-muted small mb-0">Automação completa de WhatsApp</p>
                </div>
                <div class="text-end">
                    <h3 class="mb-0">R$ {{ number_format($subscription->amount, 2, ',', '.') }}</h3>
                    <small class="text-muted">/mês</small>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">Status</small>
                    <p class="mb-0">
                        <span class="badge 
                            @if($subscription->status === 'active') bg-success
                            @elseif($subscription->status === 'pending') bg-warning
                            @else bg-danger
                            @endif">
                            @if($subscription->status === 'active')
                                <i class="fa-solid fa-check me-1"></i> Ativa
                            @elseif($subscription->status === 'pending')
                                <i class="fa-solid fa-clock me-1"></i> Pendente
                            @elseif($subscription->status === 'suspended')
                                <i class="fa-solid fa-exclamation-triangle me-1"></i> Suspensa
                            @else
                                {{ ucfirst($subscription->status) }}
                            @endif
                        </span>
                    </p>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Método de Pagamento</small>
                    <p class="mb-0">{{ $subscription->payment_method ? ucfirst($subscription->payment_method) : 'Não configurado' }}</p>
                </div>

                @if($subscription->current_period_start)
                    <div class="col-md-6">
                        <small class="text-muted">Período Atual</small>
                        <p class="mb-0 small">
                            {{ $subscription->current_period_start->format('d/m/Y') }} - 
                            {{ $subscription->current_period_end->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted">Próxima Cobrança</small>
                        <p class="mb-0">{{ $subscription->current_period_end->format('d/m/Y') }}</p>
                    </div>
                @endif

                <div class="col-md-6">
                    <small class="text-muted">Ciclo de Cobrança</small>
                    <p class="mb-0">{{ ucfirst($subscription->billing_cycle) }}</p>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Assinatura desde</small>
                    <p class="mb-0">{{ $subscription->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            @if($subscription->status === 'active')
                <hr>
                <form method="POST" action="{{ route('dashboard.subscription.cancel') }}" onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso a todas as funcionalidades.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-xmark me-1"></i>
                        Cancelar Assinatura
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Recursos Inclusos -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Recursos Inclusos</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <p class="mb-1 fw-medium">Flow Builder Visual</p>
                            <p class="text-muted small mb-0">Crie fluxos de automação sem código</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <p class="mb-1 fw-medium">WhatsApp Ilimitado</p>
                            <p class="text-muted small mb-0">Mensagens ilimitadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <p class="mb-1 fw-medium">Gerenciamento de Leads</p>
                            <p class="text-muted small mb-0">Organize seus contatos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <p class="mb-1 fw-medium">Suporte por Email</p>
                            <p class="text-muted small mb-0">Resposta em até 24h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Pagamentos -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Histórico de Pagamentos</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Método</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <span class="small">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td>
                                <span class="fw-medium">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($payment->status === 'paid') bg-success
                                    @elseif($payment->status === 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ ucfirst($payment->payment_method) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Nenhum pagamento registrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
@else
    <!-- Sem Assinatura -->
    <div class="card shadow-sm border-0 text-center">
        <div class="card-body p-5">
            <i class="fa-solid fa-exclamation-circle text-warning mb-3" style="font-size: 4rem;"></i>
            <h4 class="mb-3">Você não possui uma assinatura ativa</h4>
            <p class="text-muted mb-4">
                Assine agora para ter acesso a todas as funcionalidades da plataforma
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary">
                <i class="fa-solid fa-rocket me-2"></i>
                Assinar Agora
            </a>
        </div>
    </div>
@endif
@endsection
