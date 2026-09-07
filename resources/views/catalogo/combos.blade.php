@extends('layouts.app')
@section('title', 'Combos - Catálogo')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Catálogo</p>
        <h1 class="page-title">Combos</h1>
        <p class="page-sub">Gestiona los combos del menú</p>
    </div>
    <div class="head-actions">
        <a href="{{ route('catalogo.productos.index') }}" class="btn-ghost-brand"><i class="bi bi-box-seam"></i> Productos</a>
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalCombo"><i class="bi bi-plus-lg"></i> Nuevo combo</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar combo…" class="input-brand" aria-label="Buscar combo" />
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaCombos" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th class="col-num">Precio USD</th>
                <th>Estado</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="modalCombo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formCombo" class="ajax-form" method="POST" action="{{ route('catalogo.combos.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title">Nuevo combo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre <span class="req">*</span></label>
                        <input type="text" name="nombre" class="input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255" placeholder="Ej. Combo Especial">
                    </div>
                    <div class="field">
                        <label class="label">Categoría <span class="req">*</span></label>
                        <select name="categoria_id" class="select-brand" required>
                            <option value="">Selecciona categoría</option>
                            @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="label">Precio USD <span class="req">*</span></label>
                        <div class="input-money">
                            <span class="pre">$</span>
                            <input type="number" name="precio_usd" class="input-brand padx" step="0.01" min="0" value="0" required>
                        </div>
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
    initDataTable('tablaCombos', '{{ route("catalogo.combos.data") }}', [
        {data:'nombre',name:'nombre'},
        {data:'categoria.nombre',name:'categoria_id'},
        {data:'precio_usd',name:'precio_usd'},
        {data:'activo',name:'activo'},
        {data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
