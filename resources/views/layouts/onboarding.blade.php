<!doctype html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Cadastro | Zaptria')</title>
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

    @stack('styles')

    <style>
        /* Stepper */
        .onboarding-stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 2rem;
        }

        .stepper-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .stepper-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid var(--bs-border-color);
            background-color: var(--bs-body-bg);
            color: var(--bs-secondary-color);
            transition: all 0.3s ease;
        }

        .stepper-step.active .stepper-circle {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.2);
        }

        .stepper-step.completed .stepper-circle {
            background-color: var(--bs-success);
            border-color: var(--bs-success);
            color: #fff;
        }

        .stepper-label {
            font-size: 0.75rem;
            margin-top: 0.4rem;
            color: var(--bs-secondary-color);
            font-weight: 500;
            white-space: nowrap;
        }

        .stepper-step.active .stepper-label {
            color: var(--bs-primary);
            font-weight: 600;
        }

        .stepper-step.completed .stepper-label {
            color: var(--bs-success);
        }

        .stepper-line {
            width: 60px;
            height: 2px;
            background-color: var(--bs-border-color);
            margin: 0 0.5rem;
            margin-bottom: 1.4rem;
            transition: background-color 0.3s ease;
        }

        .stepper-line.completed {
            background-color: var(--bs-success);
        }

        @media (min-width: 576px) {
            .stepper-line {
                width: 100px;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="container py-4 flex-fill">
        {{-- Logo --}}
        <div class="text-center mb-4">
            <picture>
                <img 
                    src="{{ asset('img/zaptria-dark.webp') }}" 
                    alt="Zaptria" 
                    class="logo-light d-none"
                    height="40"
                >
                <img 
                    src="{{ asset('img/zaptria-white.webp') }}" 
                    alt="Zaptria" 
                    class="logo-dark d-none"
                    height="40"
                >
            </picture>
        </div>

        {{-- Stepper --}}
        @php $step = $currentStep ?? 1; @endphp
        <div class="onboarding-stepper">
            {{-- Step 1 --}}
            <div class="stepper-step {{ $step >= 2 ? 'completed' : ($step == 1 ? 'active' : '') }}">
                <div class="stepper-circle">
                    @if($step >= 2)
                        <i class="fa-solid fa-check fa-sm"></i>
                    @else
                        1
                    @endif
                </div>
                <span class="stepper-label">Seus Dados</span>
            </div>

            <div class="stepper-line {{ $step >= 2 ? 'completed' : '' }}"></div>

            {{-- Step 2 --}}
            <div class="stepper-step {{ $step >= 3 ? 'completed' : ($step == 2 ? 'active' : '') }}">
                <div class="stepper-circle">
                    @if($step >= 3)
                        <i class="fa-solid fa-check fa-sm"></i>
                    @else
                        2
                    @endif
                </div>
                <span class="stepper-label">Sua Empresa</span>
            </div>

            <div class="stepper-line {{ $step >= 3 ? 'completed' : '' }}"></div>

            {{-- Step 3 --}}
            <div class="stepper-step {{ $step == 3 ? 'active' : '' }}">
                <div class="stepper-circle">3</div>
                <span class="stepper-label">Pagamento</span>
            </div>
        </div>

        {{-- Content --}}
        @yield('content')
    </div>

    {{-- Footer --}}
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

    @stack('scripts')
</body>
</html>
