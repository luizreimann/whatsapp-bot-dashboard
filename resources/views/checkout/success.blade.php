@extends('layouts.app')

@section('title', 'Pagamento Confirmado | Zaptria')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body p-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-check text-success" style="font-size: 2.5rem;"></i>
                    </div>
                </div>

                <h2 class="mb-3">Pagamento Confirmado!</h2>
                <p class="text-muted mb-4">
                    Sua assinatura foi ativada com sucesso.<br>
                    Estamos preparando tudo para você começar.
                </p>

                <div class="card border mb-4">
                    <div class="card-body p-4 text-start">
                        <h5 class="mb-3 text-center">Próximos Passos</h5>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                                <span class="fw-bold text-primary">1</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Acesse o Dashboard</h6>
                                <p class="text-muted small mb-0">Configure sua instância do WhatsApp</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                                <span class="fw-bold text-primary">2</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Conecte seu WhatsApp</h6>
                                <p class="text-muted small mb-0">Escaneie o QR Code para conectar</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                                <span class="fw-bold text-primary">3</span>
                            </div>
                            <div>
                                <h6 class="mb-1">Crie seu Primeiro Fluxo</h6>
                                <p class="text-muted small mb-0">Use nosso Flow Builder visual</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <a href="{{ route('dashboard.home') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-arrow-right me-2"></i>
                        Ir para o Dashboard
                    </a>
                </div>

                <div class="border-top pt-4">
                    <p class="text-muted small mb-0">
                        Precisa de ajuda? Entre em contato pelo email:<br>
                        <a href="mailto:suporte@zaptria.com">suporte@zaptria.com</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small">
                Você receberá um email com os detalhes da sua assinatura.<br>
                A próxima cobrança será em {{ now()->addMonth()->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>
@endsection
