@extends('layouts.app')
@section('title', 'Cierre de Jornada')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Sistema</div>
        <h1 class="page-title">Cierre de Jornada</h1>
        <p class="page-sub">Resumen y cierre de la jornada actual</p>
    </div>
</div>

@if($jornadaAbierta)
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-head"><div class="panel-title">Jornada Abierta</div></div>
    <div class="panel-body">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            <div class="card-kpi">
                <div class="kpi-label">Inicio de jornada</div>
                <div class="kpi-value" style="font-size:18px;">{{ $jornadaAbierta->fecha_apertura ? $jornadaAbierta->fecha_apertura->format('d/m/Y H:i') : 'N/A' }}</div>
            </div>
            <div class="card-kpi">
                <div class="kpi-label">Estado</div>
                <div class="kpi-value" style="font-size:18px; color:var(--success);">Abierta</div>
            </div>
            <div class="card-kpi">
                <div class="kpi-label">Usuario</div>
                <div class="kpi-value" style="font-size:18px;">{{ $jornadaAbierta->usuario->nombre_completo ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Cierre de Jornada</div></div>
    <div class="panel-body">
        <p style="color:var(--muted); margin-bottom:16px;">Al cerrar la jornada se generará un reporte con el resumen de ventas del día.</p>
        <button class="btn-primary-brand" onclick="Swal.fire({title:'¿Cerrar jornada?',text:'Se generará el reporte del día.',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, cerrar',cancelButtonText:'Cancelar'}).then(function(r){if(r.isConfirmed){showToast('Jornada cerrada','success');}})">
            <i class="bi bi-calendar-check"></i> Cerrar Jornada
        </button>
    </div>
</div>
@else
<div class="panel">
    <div class="panel-body" style="text-align:center; padding:48px;">
        <i class="bi bi-calendar-x" style="font-size:48px; color:var(--meta);"></i>
        <h3 style="margin-top:16px;">No hay jornada abierta</h3>
        <p style="color:var(--muted);">No se encontró una jornada activa para cerrar.</p>
        <a href="{{ route('jornada.apertura') }}" class="btn-primary-brand" style="margin-top:16px; display:inline-flex;">
            <i class="bi bi-calendar-plus"></i> Abrir Jornada
        </a>
    </div>
</div>
@endif
@endsection
