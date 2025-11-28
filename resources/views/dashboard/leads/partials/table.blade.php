@php
    $currentSort = $sort ?? 'created_at';
    $currentDirection = $direction ?? 'desc';

    function sortIconClass($col, $currentSort, $currentDirection) {
        if ($col !== $currentSort) {
            return 'fa-solid fa-arrow-up-wide-short opacity-25';
        }

        return $currentDirection === 'asc'
            ? 'fa-solid fa-arrow-up-wide-short'
            : 'fa-solid fa-arrow-down-wide-short';
    }

    function nextDirection($col, $currentSort, $currentDirection) {
        if ($col !== $currentSort) {
            return 'asc';
        }

        return $currentDirection === 'asc' ? 'desc' : 'asc';
    }
@endphp

<div class="p-0">

    <table class="table table-hover table-striped mb-0">
        <thead>
            <tr>
                <th class="font-regular text-small text-primary">Nome</th>
                <th class="font-regular text-small text-primary">Telefone</th>

                <th>
                    <button
                        type="button"
                        class="btn btn-link btn-sm p-0 sort-link text-decoration-none not-shadow"
                        data-sort="flux"
                        data-direction="{{ nextDirection('flux', $currentSort, $currentDirection) }}"
                    >
                        Fluxo
                        <i class="{{ sortIconClass('flux', $currentSort, $currentDirection) }} ms-1"></i>
                    </button>
                </th>

                <th>
                    <button
                        type="button"
                        class="btn btn-link btn-sm p-0 sort-link text-decoration-none not-shadow"
                        data-sort="status"
                        data-direction="{{ nextDirection('status', $currentSort, $currentDirection) }}"
                    >
                        Status
                        <i class="{{ sortIconClass('status', $currentSort, $currentDirection) }} ms-1"></i>
                    </button>
                </th>

                <th>
                    <button
                        type="button"
                        class="btn btn-link btn-sm p-0 sort-link text-decoration-none not-shadow"
                        data-sort="created_at"
                        data-direction="{{ nextDirection('created_at', $currentSort, $currentDirection) }}"
                    >
                        Criado em
                        <i class="{{ sortIconClass('created_at', $currentSort, $currentDirection) }} ms-1"></i>
                    </button>
                </th>

                <th></th>
            </tr>
        </thead>

        <tbody>
            @forelse ($leads as $lead)
                <tr class="align-middle">
                    <td>{{ $lead->name }}</td>

                    <td>{{ $lead->phone }}</td>

                    <td>
                        @if ($lead->flux)
                            <span class="badge text-dark bg-success">{{ $lead->flux->name }}</span>
                        @else
                            <span class="badge bg-secondary">Nenhum</span>
                        @endif
                    </td>

                    <td>
                        <span class="badge bg-info text-dark text-capitalize">
                            {{ $lead->status->label() }}
                        </span>
                    </td>

                    <td>
                        {{ $lead->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        <a href="{{ route('dashboard.leads.show', $lead) }}" class="btn not-shadow btn-sm btn-outline-primary" aria-label="Ver detalhes">
                            <i class="fa-solid fa-circle-chevron-right"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Nenhum lead encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<div class="mt-3">
    {{ $leads->links('components.pagination') }}
</div>