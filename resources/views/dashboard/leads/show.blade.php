@extends('layouts.app')

@section('title', "Lead #{$lead->id} | Zaptria")

@php
    $phoneDigits = preg_replace('/\D+/', '', $lead->phone ?? '');
    $waLink = $phoneDigits ? "https://wa.me/{$phoneDigits}" : null;

    $last_contact = $lead->data['last_contact'] ?? null;
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('dashboard.leads') }}" class="btn not-shadow btn-sm btn-outline-secondary mb-2">
            <i class="fa-solid fa-chevron-left me-1"></i> Voltar para Leads
        </a>
        <h1 class="h3 fw-bold mb-0 mt-1">
            Lead #{{ $lead->id }}
        </h1>
    </div>

    @if ($waLink)
        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
           class="btn btn-success d-flex align-items-center gap-2">
            <i class="fa-brands fa-whatsapp"></i>
            Entrar em contato
        </a>
    @endif
</div>

<div class="row g-4">

    {{-- Bloco principal de informações --}}
    <div class="col-12 col-lg-8">
        <div class="card not-animate shadow-sm border-0">
            <div class="card-body">

                <h2 class="h5 fw-semibold mb-3">Informações do Lead</h2>

                {{-- Nome --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Nome</small>
                    <div class="d-flex align-items-center justify-content-between copyable-field"
                         data-copy-value="{{ $lead->name }}">
                        <span class="fw-semibold">{{ $lead->name }}</span>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary copy-trigger"
                                aria-label="Copiar nome">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                {{-- Telefone --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Telefone</small>
                    <div class="d-flex align-items-center justify-content-between copyable-field"
                         data-copy-value="{{ $lead->phone }}">
                        <span>{{ $lead->phone }}</span>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary copy-trigger"
                                aria-label="Copiar telefone">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Email</small>
                    <div class="d-flex align-items-center justify-content-between copyable-field"
                         data-copy-value="{{ $lead->email ?? '' }}">
                        <span>{{ $lead->email ?? '—' }}</span>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary copy-trigger"
                                aria-label="Copiar email">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                {{-- Fluxo --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Fluxo</small>
                    <span class="badge text-dark bg-success">
                        {{ optional($lead->flux)->name ?? 'Nenhum' }}
                    </span>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Status</small>
                    @php
                        // Se estiver usando enum:
                        $statusLabel = method_exists($lead->status, 'label')
                            ? $lead->status->label()
                            : ucfirst($lead->status);
                    @endphp
                    <span class="badge bg-info text-dark text-capitalize">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Origem --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Origem</small>
                    <span class="badge bg-info text-dark">
                        {{ ucfirst($lead->source) }}
                    </span>
                </div>

                {{-- Datas --}}
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Criado em</small>
                        <span>{{ $lead->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1">Atualizado em</small>
                        <span>{{ $lead->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card not-animate shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3 justify-content-between">
                    <h2 class="h6 fw-semibold mb-0">Notas</h2>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                        data-inline-edit
                        data-inline-target="#lead-notes-text"
                        data-inline-url="{{ route('dashboard.leads.notes.update', $lead) }}"
                        data-inline-field="notes"
                        data-inline-type="textarea"
                        aria-label="Editar notas"
                    >
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>

                <p id="lead-notes-text" class="mb-0 text-muted">
                    {{ $lead->data['notes'] ?? 'Nenhuma anotação adicionada ainda.' }}
                </p>
            </div>
        </div>

        <div class="card not-animate shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6 fw-semibold mb-3">Metadados</h2>
                <dl class="row mb-0 small">
                    @if (!empty($lead->data['utm_source']))
                        <dt class="col-5 text-muted">UTM Source</dt>
                        <dd class="col-7">{{ $lead->data['utm_source'] }}</dd>
                    @endif

                    @if (!empty($last_contact))
                        <dt class="col-5 text-muted">Último contato</dt>
                        <dd class="col-7">{{ $last_contact ? \Carbon\Carbon::parse($last_contact)->format('d/m/Y H:i') : '—' }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

</div>

@vite('resources/js/pages/leads.js')

@endsection