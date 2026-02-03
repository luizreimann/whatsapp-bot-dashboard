@extends('layouts.app')

@section('title', 'Conectar integração')

@section('content')
<div class="container py-4">
    <a href="{{ route('dashboard.integrations.index') }}" class="btn btn-link btn-sm mb-3">
        &larr; Voltar para integrações
    </a>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        @if(!empty($meta['icon']))
                            <img
                                src="{{ asset('img/icons/' . $meta['icon']) }}"
                                alt="{{ $meta['label'] ?? 'Ícone' }}"
                                class="me-2"
                                width="28"
                                height="28"
                            >
                        @endif

                        <h1 class="h4 mb-0">
                            {{ $meta['label'] ?? strtoupper($provider->value) }}
                        </h1>
                    </div>

                    @if(!empty($meta['description']))
                        <p class="text-muted mb-4">
                            {{ $meta['description'] }}
                        </p>
                    @endif

                    <form method="POST" action="{{ route('dashboard.integrations.connect.store', $provider->value) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nome da conexão</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $account->name ?? $meta['label'] ?? '') }}"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo genérico para auth_type = api_token --}}
                        @if(($meta['auth_type'] ?? null) === 'api_token')
                            <div class="mb-3">
                                <label class="form-label">API Key / Token</label>
                                <input
                                    type="text"
                                    name="api_key"
                                    class="form-control @error('api_key') is-invalid @enderror"
                                    value="{{ old('api_key', data_get($account, 'config.api_key')) }}"
                                >
                                @error('api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Cole aqui o token ou chave de API fornecido pelo {{ $meta['label'] ?? 'provedor' }}.
                                </div>
                            </div>
                        @endif

                        {{-- Espaço reservado para campos específicos por provider no futuro --}}
                        {{--
                        @includeIf("dashboard.integrations.partials.fields.{$provider->value}", [
                            'meta'    => $meta,
                            'account' => $account,
                        ])
                        --}}

                        <button type="submit" class="btn btn-primary">
                            {{ $account ? 'Salvar alterações' : 'Conectar integração' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mt-3 mt-lg-0">
            <div class="card border-0">
                <div class="card-body">
                    <h2 class="h6 mb-2">Ajuda</h2>
                    <p class="text-muted small mb-2">
                        Depois de conectar, você poderá usar esta integração em fluxos e automações.
                    </p>
                    <p class="text-muted small mb-0">
                        Podemos colocar aqui no futuro um passo a passo específico do
                        {{ $meta['label'] ?? 'provedor' }} (como gerar token, permissões, etc.).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection