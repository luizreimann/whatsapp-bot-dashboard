@extends('layouts.app')

@section('title', 'Leads | Zaptria')

@section('content')

<div class="row align-items-center mb-4">
    <div class="col-6">
        <h1 class="h3 fw-bold">Leads</h1>
    </div>
    <div class="col-6 d-flex justify-content-end">
        <button
            class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtersCollapse"
            aria-expanded="false"
            aria-controls="filtersCollapse">
        <i class="fa-solid fa-filter"></i>
        Filtros
    </button>
    </div>
</div>


{{-- Dropdown Filtro --}}
<div class="collapse" id="filtersCollapse">

    <form method="GET" action="{{ route('dashboard.leads') }}" class="card not-animate mb-3 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                {{-- Filtro por Fluxo --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold mb-1">Fluxo</label>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($allFluxes as $flux)
                            @php
                                $checked = in_array($flux->id, $filters['flux'] ?? []);
                            @endphp
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="flux[]"
                                    id="flux_{{ $flux->id }}"
                                    value="{{ $flux->id }}"
                                    {{ $checked ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="flux_{{ $flux->id }}">
                                    {{ $flux->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Filtro por Status --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold mb-1">Status</label>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($allStatuses as $statusEnum)
                            @php
                                $value   = $statusEnum->value;
                                $checked = in_array($value, $filters['status'] ?? []);
                            @endphp
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="status[]"
                                    id="status_{{ $value }}"
                                    value="{{ $value }}"
                                    {{ $checked ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="status_{{ $value }}">
                                    {{ $statusEnum->label() }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Filtro por Data (Criado em) --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold mb-1">Criado em</label>

                    <div class="row g-2">
                        <div class="col-6">
                            <input
                                type="date"
                                name="date_from"
                                class="form-control"
                                value="{{ $filters['date_from'] ?? '' }}"
                                placeholder="De"
                            >
                        </div>
                        <div class="col-6">
                            <input
                                type="date"
                                name="date_to"
                                class="form-control"
                                value="{{ $filters['date_to'] ?? '' }}"
                                placeholder="Até"
                            >
                        </div>
                    </div>
                </div>

                {{-- Ordenação preservada --}}
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                {{-- Botões --}}
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('dashboard.leads') }}" class="btn btn-outline-secondary btn-sm">
                        Limpar filtros
                    </a>

                    <button type="submit" class="btn btn-primary btn-sm">
                        Aplicar filtros
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>

<div
    id="leadsTableWrapper"
    data-url="{{ route('dashboard.leads.data') }}"
>
    @include('dashboard.leads.partials.table', [
        'leads'     => $leads,
        'sort'      => $sort ?? 'created_at',
        'direction' => $direction ?? 'desc',
    ])
</div>

@vite('resources/js/pages/leads.js')

@endsection