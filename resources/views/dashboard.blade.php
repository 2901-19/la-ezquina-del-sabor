@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Panel del día</p>
        <h1 class="page-title">Resumen de la jornada</h1>
        <p class="page-sub">Estado actual del negocio y comandas activas en tiempo real.</p>
    </div>
    <a href="{{ route('comandas.index') }}" class="btn-primary-brand">
        <i class="bi bi-plus-lg"></i> Nueva comanda
    </a>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card-kpi accent">
            <div class="kpi-label"><i class="bi bi-bank"></i> Ventas del día</div>
            <div class="kpi-value">${{ number_format($ventasHoy, 2, ',', '.') }} <span class="unit">USD</span></div>
            <div class="kpi-meta"><span class="kpi-bs">Bs {{ number_format($ventasHoy * (float)$tasaBcv, 2, ',', '.') }}</span></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-kpi">
            <div class="kpi-label"><i class="bi bi-receipt"></i> Comandas activas</div>
            <div class="kpi-value">{{ $comandasActivas->count() }}</div>
            <div class="kpi-meta">{{ $pedidosHoy }} pedidos hoy</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-kpi">
            <div class="kpi-label"><i class="bi bi-people"></i> Clientes atendidos</div>
            <div class="kpi-value">{{ $totalClientes }}</div>
            <div class="kpi-meta">Registrados</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-kpi">
            <div class="kpi-label"><i class="bi bi-currency-dollar"></i> Tasa BCV</div>
            <div class="kpi-value" style="font-family:var(--font-mono);">{{ number_format((float)$tasaBcv, 2, ',', '.') }}</div>
            <div class="kpi-meta">Bs/USD</div>
        </div>
    </div>
</div>

{{-- Contenido principal --}}
<div class="row g-4 align-items-start">
    {{-- Comandas activas --}}
    <div class="col-12 col-xl-8">
        <div class="panel-comandas">
            <div class="panel-head">
                <h2 class="panel-title">Comandas activas <span class="count">{{ $comandasActivas->count() }}</span></h2>
                <a href="{{ route('comandas.index') }}" class="panel-link" style="font-size:13.5px;font-weight:600;color:var(--accent-ghost);">Ver todas &rarr;</a>
            </div>

            @forelse($comandasActivas as $comanda)
                <div class="comanda-row">
                    <span class="comanda-num">{{ str_pad($comanda->numero_correlativo_diario, 3, '0', STR_PAD_LEFT) }}</span>
                    <div class="comanda-info">
                        <div class="comanda-cliente">{{ $comanda->cliente->nombre ?? $comanda->nombre_cliente_temporal ?? 'Sin cliente' }}</div>
                        <div class="comanda-meta">{{ $comanda->comandaDetalles->count() }} producto{{ $comanda->comandaDetalles->count() !== 1 ? 's' : '' }} &middot; <span class="tipo">{{ ucfirst($comanda->estado_comanda) }}</span></div>
                    </div>
                    @php
                        $estadoClass = match($comanda->estado_comanda) {
                            'montar' => 'montar',
                            'entrega' => 'entrega',
                            'cobrar' => 'cobrar',
                            default => 'montar',
                        };
                    @endphp
                    <span class="badge-estado {{ $estadoClass }}"><span class="dot"></span>{{ ucfirst($comanda->estado_comanda) }}</span>
                    <div class="comanda-total">
                        <div class="usd">${{ number_format($comanda->total_usd, 2, ',', '.') }}</div>
                        <div class="bs">Bs {{ number_format($comanda->total_usd * (float)$tasaBcv, 2, ',', '.') }}</div>
                    </div>
                    <a href="{{ route('comandas.index') }}" class="btn-row-open" aria-label="Abrir comanda {{ $comanda->numero_correlativo_diario }}"><i class="bi bi-chevron-right"></i></a>
                </div>
            @empty
                <div style="padding:32px 20px; text-align:center; color:var(--muted);">
                    No hay comandas activas en este momento.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Lateral --}}
    <div class="col-12 col-xl-4">
        {{-- Tasa BCV --}}
        <div class="panel-tasa mb-4">
            <div class="panel-tasa-label">Tasa BCV del día</div>
            <div class="panel-tasa-value">Bs {{ number_format((float)$tasaBcv, 2, ',', '.') }} <span>/ 1 USD</span></div>
            <div class="panel-tasa-update">
                <a href="{{ route('sistema.configuracion') }}" class="btn btn-ghost-brand btn-ghost-on-dark"><i class="bi bi-arrow-clockwise"></i> Actualizar tasa</a>
            </div>
        </div>

        {{-- Estado de jornada --}}
        <div class="card-kpi mb-4">
            <div class="kpi-label"><i class="bi bi-calendar-check"></i> Estado de jornada</div>
            <div class="kpi-meta" style="font-size:14px;color:var(--fg-2);margin-top:12px;">Jornada abierta. {{ $pedidosHoy }} pedidos registrados.</div>
        </div>

        {{-- Acceso rápido --}}
        <div class="card-kpi">
            <div class="kpi-label"><i class="bi bi-lightning-charge"></i> Acceso rápido</div>
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px;">
                <a href="{{ route('comandas.index') }}" class="btn-ghost-brand" style="justify-content:flex-start;">
                    <i class="bi bi-receipt"></i> Gestión de comandas
                </a>
                <a href="{{ route('catalogo.productos.index') }}" class="btn-ghost-brand" style="justify-content:flex-start;">
                    <i class="bi bi-box-seam"></i> Catálogo de productos
                </a>
                <a href="{{ route('inventario.materias-primas.index') }}" class="btn-ghost-brand" style="justify-content:flex-start;">
                    <i class="bi bi-dropbox"></i> Inventario
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
