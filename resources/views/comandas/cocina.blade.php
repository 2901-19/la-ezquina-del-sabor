@extends('layouts.app')
@section('title', 'Cocina')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Operación</div>
        <h1 class="page-title">Cocina - Kanban</h1>
        <p class="page-sub">Seguimiento de preparación de comandas</p>
    </div>
</div>
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
    <div class="panel">
        <div class="panel-head" style="background:var(--accent);"><div class="panel-title" style="color:#fff;">Montar</div></div>
        <div class="panel-body" id="colMontar">
            <p style="color:#7a7066;font-size:14px;">No hay comandas para montar.</p>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head" style="background:var(--warn);"><div class="panel-title" style="color:#fff;">Entrega</div></div>
        <div class="panel-body" id="colEntrega">
            <p style="color:#7a7066;font-size:14px;">No hay comandas en entrega.</p>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head" style="background:var(--info);"><div class="panel-title" style="color:#fff;">Cobrar</div></div>
        <div class="panel-body" id="colCobrar">
            <p style="color:#7a7066;font-size:14px;">No hay comandas para cobrar.</p>
        </div>
    </div>
</div>
@endsection
