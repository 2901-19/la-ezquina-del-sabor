<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'La Esquina del Sabor')</title>
    @vite(['resources/css/app.css'])
    @yield('styles')
</head>
<body>
    <div class="app-shell">
        @include('layouts.partials.sidebar')
        <div class="main">
            @include('layouts.partials.topbar')
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="toast-slot" id="toastSlot"></div>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
