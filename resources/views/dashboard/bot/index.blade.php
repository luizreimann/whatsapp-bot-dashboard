@extends('layouts.app')

@section('title', 'Robô | Zaptria')

@section('content')

@php
    $status  = $instance->status ?? 'inactive';
    $number  = $instance->number ?? null;

    $statusLabels = [
        'inactive'   => ['label' => 'Inativo', 'class' => 'secondary', 'icon' => 'fa-circle-minus'],
        'starting'   => ['label' => 'Iniciando...', 'class' => 'info', 'icon' => 'fa-spinner fa-spin'],
        'qr_ready'   => ['label' => 'Aguardando QR Code', 'class' => 'warning', 'icon' => 'fa-qrcode'],
        'connected'  => ['label' => 'Conectado', 'class' => 'success', 'icon' => 'fa-circle-check'],
        'error'      => ['label' => 'Erro de conexão', 'class' => 'danger', 'icon' => 'fa-triangle-exclamation'],
    ];

    $display = $statusLabels[$status] ?? $statusLabels['inactive'];
@endphp

<div class="row mb-4">
    <div class="col">
        <h1 class="h4 fw-semibold">Gerenciar Robô</h1>
        <p class="text-muted small mb-0">
            Controle do bot do WhatsApp para o tenant <strong>{{ $tenant->name }}</strong>
        </p>
    </div>
</div>

{{-- CARD DE STATUS --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-4">

                <h6 class="fw-semibold mb-3">
                    <i class="fa-solid {{ $display['icon'] }} text-{{ $display['class'] }} me-1"></i>
                    Status Atual:
                    <span class="badge bg-{{ $display['class'] }}">
                        {{ $display['label'] }}
                    </span>
                </h6>

                @if ($number)
                    <p class="mb-2 small text-muted">
                        <i class="fa-solid fa-phone me-1"></i>
                        Número conectado: <strong>{{ $number }}</strong>
                    </p>
                @endif

                <div class="mt-3">
                    @if ($status === 'inactive')
                        <a href="#" class="btn btn-primary">
                            <i class="fa-solid fa-link me-1"></i> Ativar Robô
                        </a>
                    @elseif ($status === 'qr_ready')
                        <a href="#" class="btn btn-warning text-dark">
                            <i class="fa-solid fa-qrcode me-1"></i> Ver QR Code
                        </a>
                    @elseif ($status === 'connected')
                        <a href="#" class="btn btn-outline-danger">
                            <i class="fa-solid fa-power-off me-1"></i> Desconectar
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection