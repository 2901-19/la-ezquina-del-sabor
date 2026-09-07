@extends('layouts.app')
@section('title', 'Comandas')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Operación</p>
        <h1 class="page-title">Comandas</h1>
        <p class="page-sub">Gestiona las comandas del sistema</p>
    </div>
    <div class="head-actions">
        <a href="{{ route('cocina.index') }}" class="btn-ghost-brand"><i class="bi bi-chef-hat"></i> Cocina</a>
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalComanda"><i class="bi bi-plus-lg"></i> Nueva comanda</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar comanda…" class="input-brand" aria-label="Buscar comanda" />
    </div>
    <div class="filter-selects">
        <select class="select-brand" aria-label="Filtrar por estado">
            <option value="">Todos los estados</option>
            <option>Montar</option>
            <option>Entrega</option>
            <option>Cobrar</option>
            <option>Cerrada</option>
        </select>
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaComandas" style="width:100%">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th class="col-num">Total USD</th>
                <th class="col-num">Total Bs</th>
                <th>Fecha</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="modalComanda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formComanda" class="ajax-form" method="POST" action="{{ route('comandas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title">Nueva comanda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre del cliente (temporal)</label>
                        <input type="text" name="nombre_cliente_temporal" class="input-brand" maxlength="255" placeholder="Si no está registrado">
                        <span class="help">Cliente ocasional sin registro</span>
                    </div>
                    <div class="field">
                        <label class="label">Teléfono delivery</label>
                        <input type="text" name="telefono_delivery" class="input-brand" maxlength="20" pattern="[0-9+\-\s]+">
                    </div>
                    <div class="field">
                        <label class="label">Notas</label>
                        <textarea name="notas_generales" class="textarea-brand" rows="2" maxlength="500" placeholder="Instrucciones especiales…"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Crear comanda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaComandas', '{{ route("comandas.data") }}', [
        {data:'numero_correlativo_diario',name:'numero_correlativo_diario'},
        {data:'cliente.nombre',name:'cliente_id'},{data:'estado_comanda',name:'estado_comanda'},
        {data:'total_usd',name:'total_usd'},{data:'total_ve',name:'total_ve'},
        {data:'fecha_creacion',name:'fecha_creacion'},{data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
