@extends('layouts.app')
@section('title', 'Clientes')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Gestión</p>
        <h1 class="page-title">Clientes</h1>
        <p class="page-sub">Gestiona los clientes del sistema</p>
    </div>
    <div class="head-actions">
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalCliente"><i class="bi bi-person-plus"></i> Nuevo cliente</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar por nombre o teléfono…" class="input-brand" aria-label="Buscar cliente" />
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaClientes" style="width:100%">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th class="col-num">Puntos</th>
                <th class="col-num">Compras</th>
                <th class="col-num">Saldo crédito</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formCliente" class="ajax-form" method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalClienteTitle">Nuevo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre <span class="req">*</span></label>
                        <input type="text" name="nombre" class="input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                    </div>
                    <div class="field">
                        <label class="label">Teléfono <span class="req">*</span></label>
                        <input type="text" name="telefono" class="input-brand" required pattern="[0-9+\-\s]+" maxlength="20">
                    </div>
                    <div class="field">
                        <label class="label">Dirección de delivery</label>
                        <textarea name="direccion_delivery" class="textarea-brand" rows="2" maxlength="500" placeholder="Dirección completa…"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaClientes', '{{ route("clientes.data") }}', [
        {data:'nombre',name:'nombre'},{data:'telefono',name:'telefono'},
        {data:'puntos_acumulados',name:'puntos_acumulados'},{data:'compras',name:'compras'},
        {data:'saldo',name:'saldo'},{data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
