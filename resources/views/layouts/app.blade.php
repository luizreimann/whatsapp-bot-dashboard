<!doctype html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'WhatsApp Bot Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/571e7f1ba9.js" crossorigin="anonymous"></script>

    {{-- Google Fonts - Lato --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">

    {{-- Espaço para css extra --}}
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Navbar principal --}}
    @auth
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard.home') }}">
                <picture>
                    <img 
                        src="{{ asset('img/zaptria-dark.webp') }}" 
                        alt="Zaptria" 
                        class="logo-light d-none"
                        height="32"
                    >

                    <img 
                        src="{{ asset('img/zaptria-white.webp') }}" 
                        alt="Zaptria" 
                        class="logo-dark d-none"
                        height="32"
                    >
                </picture>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                {{-- Itens do menu principal --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.home') ? 'active' : '' }}" href="{{ route('dashboard.home') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.leads') ? 'active' : '' }}" href="{{ route('dashboard.leads') }}">
                            Leads
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Fluxos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.bot') ? 'active' : '' }}" href="{{ route('dashboard.bot') }}">
                            Robô
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Integrações
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Configurações
                        </a>
                    </li>

                </ul>

                {{-- Ações do lado direito: tema + perfil --}}
                <div class="d-flex align-items-center gap-2">

                    {{-- Botão de Light/Dark mode --}}
                    <button class="btn btn-outline-secondary btn-sm" type="button" id="themeToggle" aria-label="Alternar tema">
                        <i id="themeToggleIcon" class="fa-solid fa-moon"></i>
                    </button>

                    {{-- Dropdown de perfil --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-user me-1"></i>
                            <span class="d-none d-sm-inline">
                                {{ Str::limit(auth()->user()->name, 18) }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-user me-2"></i> Conta
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-file-invoice-dollar me-2"></i> Faturamento
                                </a>
                            </li>
                            <li class="border-top"></li>
                                <a
                                    class="dropdown-item text-danger"
                                    href="{{ route('dashboard.logout', ['token' => csrf_token()]) }}"
                                >
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </nav>
    @else
        {{-- Possível navbar para usuários não autenticados --}}
    @endauth

    <main class="py-4 flex-fill">
        <div class="container">
            @yield('content')
        </div>
    </main>

    {{-- Rodapé --}}
    <footer class="border-top py-3 mt-auto">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="text-muted small">
                &copy; {{ date('Y') }} Zaptria. Todos os direitos reservados.
            </div>
            <div class="small">
                <a href="mailto:suporte@zaptria.com" class="text-decoration-underline text-link">
                    Suporte
                </a>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- JS da aplicação --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Espaço para scripts extras --}}
    @stack('scripts')
</body>
</html>