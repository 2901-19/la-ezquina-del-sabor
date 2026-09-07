@extends('layouts.app')
@section('title', 'Configuración')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Sistema</div>
        <h1 class="page-title">Configuración General</h1>
    </div>
</div>
<div class="panel" style="max-width:600px;">
    <div class="panel-head"><div class="panel-title">Datos del negocio</div></div>
    <div class="panel-body">
        <form id="formConfig" class="ajax-form" method="POST" action="{{ route('sistema.configuracion.update') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Nombre del negocio</label>
                <input type="text" class="form-control input-brand" value="La Esquina del Sabor" readonly>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Tasa BCV (Bs/USD) <span style="color:var(--danger)">*</span></label>
                <input type="number" name="tasa_bcv" class="form-control input-brand" value="{{ $tasa }}" step="0.01" min="0" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Moneda principal</label>
                <select class="form-select select-brand">
                    <option>USD</option>
                    <option>Bs</option>
                </select>
            </div>
            <button type="submit" class="btn-primary-brand">Guardar</button>
        </form>
    </div>
</div>
@endsection
