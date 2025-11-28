@if ($paginator->hasPages())
    <nav class="mt-3">
        <ul class="pagination justify-content-center mb-0">

            {{-- Página anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-pill"
                       href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       aria-label="Página anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Elementos (números / reticências) --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array de páginas --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded-pill">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-pill"
                                   href="{{ $url }}"
                                   aria-label="Ir para a página {{ $page }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Próxima página --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill"
                       href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       aria-label="Próxima página">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded-pill">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif