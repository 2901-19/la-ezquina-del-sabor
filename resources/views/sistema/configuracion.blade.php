@extends('layouts.app')
@section('title', 'Configuración')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Sistema</div>
        <h1 class="page-title">Configuración General</h1>
    </div>
</div>

<form id="formConfig" class="ajax-form" method="POST" action="{{ route('sistema.configuracion.update') }}" style="max-width:700px;">
    @csrf

    {{-- Datos del negocio --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div class="panel-title">Datos del negocio</div></div>
        <div class="panel-body">
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Nombre del negocio <span style="color:var(--danger)">*</span></label>
                <input type="text" name="nombre_negocio" class="form-control input-brand" value="{{ $configs['nombre_negocio'] ?? '' }}" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Teléfono</label>
                <input type="text" name="telefono_negocio" class="form-control input-brand" value="{{ $configs['telefono_negocio'] ?? '' }}">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Dirección</label>
                <input type="text" name="direccion_negocio" class="form-control input-brand" value="{{ $configs['direccion_negocio'] ?? '' }}">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Ruta del logo</label>
                <input type="text" name="logo_path" class="form-control input-brand" value="{{ $configs['logo_path'] ?? '' }}">
            </div>
        </div>
    </div>

    {{-- Tasa BCV --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div class="panel-title">Tasa de cambio</div></div>
        <div class="panel-body">
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Tasa BCV (Bs/USD) <span style="color:var(--danger)">*</span></label>
                <input type="number" name="tasa_bcv" class="form-control input-brand" value="{{ $configs['tasa_bcv'] ?? '818.00' }}" step="0.01" min="0" required>
            </div>
        </div>
    </div>

    {{-- Impresora --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div class="panel-title">Impresora</div></div>
        <div class="panel-body">
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Estado</label>
                <select name="impresora_activa" class="form-select select-brand">
                    <option value="0" {{ ($configs['impresora_activa'] ?? '0') === '0' ? 'selected' : '' }}>Desactivada</option>
                    <option value="1" {{ ($configs['impresora_activa'] ?? '0') === '1' ? 'selected' : '' }}>Activada</option>
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Nombre de impresora</label>
                <input type="text" name="impresora_nombre" class="form-control input-brand" value="{{ $configs['impresora_nombre'] ?? '' }}">
            </div>
        </div>
    </div>

    {{-- Puntos --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div class="panel-title">Regla de puntos</div></div>
        <div class="panel-body">
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Puntos por cada $1 USD <span style="color:var(--danger)">*</span></label>
                <input type="number" name="regla_puntos" class="form-control input-brand" value="{{ $configs['regla_puntos'] ?? '10' }}" min="0" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Valor de cada punto (USD) <span style="color:var(--danger)">*</span></label>
                <input type="number" name="puntos_valor_usd" class="form-control input-brand" value="{{ $configs['puntos_valor_usd'] ?? '0.10' }}" step="0.01" min="0" required>
            </div>
        </div>
    </div>

    {{-- Parámetros de precios --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div class="panel-title">Parámetros de precios</div></div>
        <div class="panel-body">
            <div style="margin-bottom:16px;">
                <label class="form-label-brand">Margen de ganancia default (%)</label>
                <input type="number" name="params_precios" class="form-control input-brand"
                    value="{{ isset($configs['params_precios']) ? json_decode($configs['params_precios'], true)['margen_default'] ?? 30 : 30 }}"
                    min="0" max="100">
            </div>
        </div>
    </div>

    <button type="submit" class="btn-primary-brand">Guardar configuración</button>
</form>
@endsection
