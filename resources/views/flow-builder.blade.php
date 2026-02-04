<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Zaptria') }} - Flow Builder</title>

    {{-- Google Fonts - Lato --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/js/flow-builder/main.jsx'])

    <script>
        (function() {
            const storageKey = 'dashboard-theme';
            const stored = localStorage.getItem(storageKey);
            if (stored === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>
</head>
<body class="antialiased">
    <div id="flow-builder-root" 
         data-flux="{{ json_encode($flux) }}"
         data-tenant-id="{{ auth()->user()->tenant_id }}"
         data-integrations="{{ json_encode($integrations) }}"
         data-save-url="{{ route('dashboard.fluxes.update', ['flux' => $flux->id]) }}"
         data-back-url="{{ route('dashboard.fluxes.index') }}"
         data-flux-name="{{ $flux->name }}"
    ></div>
</body>
</html>
