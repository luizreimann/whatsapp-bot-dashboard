@extends('layouts.app')

@section('title', 'Checkout | Zaptria')

@push('styles')
<style>
    #payment-element {
        margin-bottom: 1rem;
    }
    .StripeElement {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .StripeElement--focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .StripeElement--invalid {
        border-color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-lg-10">
        <h2 class="mb-1">Finalize sua assinatura</h2>
        <p class="text-muted mb-4">Você está a um passo de automatizar seu WhatsApp</p>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Layout 2 Colunas -->
        <div class="row g-4">
            <!-- Coluna Esquerda: Informações -->
            <div class="col-lg-6">
                <!-- Informações da Conta -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Informações da Conta</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Empresa:</small>
                                <p class="mb-0 fw-medium">{{ auth()->user()->tenant->name }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalhes do Plano -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Detalhes do Plano</h5>
                        
                        <div class="bg-primary bg-opacity-10 rounded p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="mb-1">Plano Mensal</h4>
                                    <p class="text-muted small mb-0">Automação completa de WhatsApp</p>
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0">R$ {{ number_format($subscription->amount, 2, ',', '.') }}</h3>
                                    <small class="text-muted">/mês</small>
                                </div>
                            </div>

                            <div class="small">
                                <p class="mb-1"><i class="fa-solid fa-check text-success me-2"></i>Flow Builder Visual</p>
                                <p class="mb-1"><i class="fa-solid fa-check text-success me-2"></i>Integração WhatsApp Ilimitada</p>
                                <p class="mb-1"><i class="fa-solid fa-check text-success me-2"></i>Gerenciamento de Leads</p>
                                <p class="mb-1"><i class="fa-solid fa-check text-success me-2"></i>Analytics Básico</p>
                                <p class="mb-0"><i class="fa-solid fa-check text-success me-2"></i>Suporte sempre que precisar</p>
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="alert alert-warning small mb-0">
                                <p class="fw-bold mb-2">
                                    <i class="fa-solid fa-user-shield me-1"></i> Admin: Ajustar Valor
                                </p>
                                <form method="POST" action="{{ route('checkout.amount.update', $subscription) }}" class="row g-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="col">
                                        <input 
                                            type="number" 
                                            name="amount" 
                                            step="0.01" 
                                            min="1" 
                                            max="9999.99"
                                            value="{{ $subscription->amount }}"
                                            class="form-control form-control-sm"
                                        >
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            Atualizar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resumo -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Resumo</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>R$ {{ number_format($subscription->amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Impostos</span>
                            <span>Inclusos</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total</h5>
                            <h4 class="mb-0">R$ {{ number_format($subscription->amount, 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Formulário de Pagamento -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="fa-solid fa-credit-card me-2"></i>
                            Dados do Cartão
                        </h5>

                        <form id="payment-form">
                            @csrf
                            
                            <!-- Stripe Payment Element -->
                            <div id="payment-element" class="mb-3">
                                <!-- Stripe.js injetará o formulário aqui -->
                            </div>

                            <!-- Mensagem de erro -->
                            <div id="payment-errors" class="alert alert-danger d-none" role="alert"></div>

                            <!-- Botão de Pagamento -->
                            <div class="d-grid mb-3">
                                <button type="submit" id="submit-button" class="btn btn-primary btn-lg">
                                    <span id="button-text">
                                        <i class="fa-solid fa-lock me-2"></i>
                                        Confirmar Pagamento
                                    </span>
                                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>

                            <div class="alert alert-info small text-center mb-0">
                                <i class="fa-solid fa-shield-halved me-1"></i>
                                Pagamento 100% seguro processado pelo Stripe<br>
                                <small class="text-muted">Seus dados são criptografados e protegidos</small>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="alert alert-light border mt-3 small">
                    <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Cobrança recorrente mensal</p>
                    <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Cancele quando quiser</p>
                    <p class="mb-0"><i class="fa-solid fa-circle-check text-success me-2"></i>Sem período de teste</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements({
        clientSecret: '{{ $clientSecret }}',
    });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const errorDiv = document.getElementById('payment-errors');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Desabilitar botão e mostrar loading
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        spinner.classList.remove('d-none');
        errorDiv.classList.add('d-none');

        try {
            // Confirmar setup com Stripe
            const {error, setupIntent} = await stripe.confirmSetup({
                elements,
                confirmParams: {
                    return_url: window.location.origin + '/checkout/success',
                },
                redirect: 'if_required',
            });

            if (error) {
                throw new Error(error.message);
            }

            // Enviar payment_method_id para o backend
            const response = await fetch('{{ route('checkout.process', $subscription) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    payment_method_id: setupIntent.payment_method,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro ao processar pagamento');
            }

            // Redirecionar para página de sucesso
            window.location.href = data.redirect;

        } catch (err) {
            // Mostrar erro
            errorDiv.textContent = err.message;
            errorDiv.classList.remove('d-none');

            // Reabilitar botão
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    });
</script>
@endpush
@endsection
