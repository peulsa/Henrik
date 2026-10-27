<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'Henrik') · Inventario TI</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>document.documentElement.dataset.theme = localStorage.getItem('henrik-theme') || 'light';</script>
</head>
<body>
    @php($platformUser = \App\Support\PlatformContext::user())
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">H</span>
                <span><strong>Henrik</strong><small>Inventario TI</small></span>
            </a>
            <nav class="nav" aria-label="Principal">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span> Resumen</a>
                <a class="{{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}"><span>□</span> Inventario</a>
                <a class="{{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}"><span>⌖</span> Ubicaciones</a>
                <a class="{{ request()->routeIs('movements.*') ? 'active' : '' }}" href="{{ route('movements.index') }}"><span>⇄</span> Movimientos</a>
                <a class="{{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}"><span>◷</span> Auditoría</a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-avatar">{{ mb_strtoupper(mb_substr($platformUser->name ?: $platformUser->email, 0, 1)) }}</div>
                <div class="user-meta"><strong>{{ $platformUser->name ?: 'Usuario' }}</strong><small>{{ $platformUser->email }}</small></div>
            </div>
        </aside>

        <main class="main">
            <header class="topbar">
                <button class="icon-button mobile-menu" type="button" data-sidebar-toggle aria-label="Abrir menú">☰</button>
                <div class="topbar-title">@yield('eyebrow', 'Municipalidad de Doñihue')</div>
                <button class="theme-button" type="button" data-theme-toggle aria-label="Cambiar tema"><span data-theme-icon>◐</span><span>Cambiar tema</span></button>
            </header>

            <div class="content">
                @if(session('success'))
                    <div class="alert success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert danger">
                        <strong>Revisa la información ingresada.</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
