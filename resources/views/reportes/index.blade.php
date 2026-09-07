@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Sistema</div>
        <h1 class="page-title">Reportes</h1>
        <p class="page-sub">Genera reportes del negocio</p>
    </div>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'ventas') }}'">
        <div class="kpi-label"><i class="bi bi-graph-up"></i> Reporte de Ventas</div>
        <div class="kpi-meta" style="margin-top:12px;">Resumen de ventas diarias, semanales y mensuales.</div>
    </div>
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'productos') }}'">
        <div class="kpi-label"><i class="bi bi-box-seam"></i> Productos Más Vendidos</div>
        <div class="kpi-meta" style="margin-top:12px;">Ranking de productos por volumen de venta.</div>
    </div>
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'clientes') }}'">
        <div class="kpi-label"><i class="bi bi-people"></i> Clientes Frecuentes</div>
        <div class="kpi-meta" style="margin-top:12px;">Top clientes por frecuencia y monto de compra.</div>
    </div>
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'inventario') }}'">
        <div class="kpi-label"><i class="bi bi-dropbox"></i> Inventario</div>
        <div class="kpi-meta" style="margin-top:12px;">Estado actual del inventario y movimientos.</div>
    </div>
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'creditos') }}'">
        <div class="kpi-label"><i class="bi bi-cash-stack"></i> Créditos</div>
        <div class="kpi-meta" style="margin-top:12px;">Estado de créditos y abonos pendientes.</div>
    </div>
    <div class="card-kpi" style="cursor:pointer;" onclick="window.location.href='{{ route('reportes.exportar', 'jornadas') }}'">
        <div class="kpi-label"><i class="bi bi-calendar-check"></i> Jornadas</div>
        <div class="kpi-meta" style="margin-top:12px;">Historial de apertura y cierre de jornadas.</div>
    </div>
</div>
@endsection
