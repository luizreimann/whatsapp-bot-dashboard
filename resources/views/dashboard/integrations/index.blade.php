@extends('layouts.app')

@section('title', 'Integrações')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Integrações</h1>
            <p class="text-muted mb-0">
                Conecte CRMs, gateways de pagamento e outras ferramentas para deixar seu fluxo mais inteligente.
            </p>
        </div>
    </div>

    @forelse($categories as $category => $providers)
        <div class="mb-4">
            <h2 class="h5 mb-3">
                {{ \App\Enums\IntegrationCategory::from($category)->label() }}
            </h2>

            <div class="row g-3">
                @foreach($providers as $item)
                    @php
                        $meta      = $item['meta'];
                        $account   = $item['account'] ?? null;
                        $connected = $item['connected'] ?? false;
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    @if(!empty($meta['icon']))
                                        <img
                                            src="{{ asset('img/icons/' . $meta['icon']) }}"
                                            alt="{{ $meta['label'] ?? 'Ícone' }}"
                                            class="me-2"
                                            width="24"
                                            height="24"
                                        >
                                    @endif

                                    <h3 class="h6 mb-0">
                                        {{ $meta['label'] ?? strtoupper($item['provider']) }}
                                    </h3>
                                </div>

                                @if(!empty($meta['description']))
                                    <p class="text-muted small mb-3">
                                        {{ $meta['description'] }}
                                    </p>
                                @endif

                                @if($account && $account->name)
                                    <p class="text-muted small mb-3">
                                        Conexão: <strong>{{ $account->name }}</strong>
                                    </p>
                                @endif

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $connected ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $connected ? 'Conectado' : 'Não conectado' }}
                                    </span>

                                    <a href="{{ route('dashboard.integrations.connect', $item['provider']) }}"
                                    class="btn btn-sm btn-primary">
                                        {{ $connected ? 'Editar' : 'Conectar' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhuma integração disponível.</p>
    @endforelse
</div>
@endsection