@extends('layouts.app')

@section('title', 'Dashboard | Zaptria')

@section('content')

@php
    // Placeholder temporário para valores
    $robotStatus = $tenant->whatsappInstance->status ?? 'inactive';
    $robotNumber = $tenant->whatsappInstance->number ?? null;

    $contactsInitiated = 12;     // placeholder
    $journeysDropped   = 4;      // placeholder
    $leadsCollected    = 7;      // placeholder
@endphp

<div class="row mb-4">
    <div class="col">
        <h1 class="h4 fw-semibold">Olá, {{ auth()->user()->name }}!</h1>
        <p class="text-muted small mb-0">
            Bem-vindo ao seu painel de controle do WhatsApp Bot.
        </p>
    </div>
</div>

{{-- STATUS DO ROBÔ --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <h6 class="fw-semibold mb-1">Status do Robô</h6>

                    <p class="mb-0 small text-muted">
                        @if ($robotStatus === 'connected')
                            <span class="badge bg-success">
                                <i class="fa-solid fa-circle-check me-1"></i> Conectado
                            </span>
                            @if ($robotNumber)
                                <span class="ms-2 text-muted">
                                    <i class="fa-solid fa-phone"></i> {{ $robotNumber }}
                                </span>
                            @endif
                        @elseif ($robotStatus === 'qr_ready')
                            <span class="badge bg-warning">
                                <i class="fa-solid fa-qrcode me-1"></i> Aguardando leitura do QR Code
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fa-solid fa-circle-minus me-1"></i> Inativo
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <a href="{{ route('dashboard.bot') }}" class="btn btn-outline-primary btn-sm">
                        Gerenciar Robô
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MÉTRICAS RÁPIDAS --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="rounded-circle square-icon bg-primary bg-opacity-10 text-primary p-3">
                        <i class="fa-regular fa-message fa-lg"></i>
                    </span>
                </div>
                <div>
                    <h6 class="fw-semibold mb-0">{{ $contactsInitiated }}</h6>
                    <p class="text-muted small mb-0">Contatos iniciados</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="rounded-circle square-icon bg-warning bg-opacity-10 text-warning p-3">
                        <i class="fa-solid fa-road-barrier fa-lg"></i>
                    </span>
                </div>
                <div>
                    <h6 class="fw-semibold mb-0">{{ $journeysDropped }}</h6>
                    <p class="text-muted small mb-0">Jornadas interrompidas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="rounded-circle square-icon bg-success bg-opacity-10 text-success p-3">
                        <i class="fa-solid fa-user-plus fa-lg"></i>
                    </span>
                </div>
                <div>
                    <h6 class="fw-semibold mb-0">{{ $leadsCollected }}</h6>
                    <p class="text-muted small mb-0">Leads coletados</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ATALHOS --}}
<div class="row g-3">
    <div class="col-12">
        <h6 class="fw-semibold mb-2">Atalhos</h6>
    </div>

    {{-- Leads --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="{{ route('dashboard.leads') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-address-book fa-2x mb-2 text-primary"></i>
                <span class="fw-semibold small">Leads</span>
            </div>
        </a>
    </div>

    {{-- Fluxos --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-diagram-project fa-2x mb-2 text-warning"></i>
                <span class="fw-semibold small">Fluxos</span>
            </div>
        </a>
    </div>

    {{-- Robô --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="{{ route('dashboard.bot') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-robot fa-2x mb-2 text-success"></i>
                <span class="fw-semibold small">Robô</span>
            </div>
        </a>
    </div>

    {{-- Integrações --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-plug fa-2x mb-2 text-info"></i>
                <span class="fw-semibold small">Integrações</span>
            </div>
        </a>
    </div>

    {{-- Configurações --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-gear fa-2x mb-2 text-secondary"></i>
                <span class="fw-semibold small">Configurações</span>
            </div>
        </a>
    </div>

    {{-- Faturamento --}}
    <div class="col-md-2 col-sm-4 col-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm border-0 text-center p-3 h-100 align-items-center">
                <i class="fa-solid fa-credit-card fa-2x mb-2 text-primary"></i>
                <span class="fw-semibold small">Faturamento</span>
            </div>
        </a>
    </div>
</div>

@endsection