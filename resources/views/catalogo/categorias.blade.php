@extends('layouts.app')
@section('title', 'Categorías - Catálogo')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Catálogo</p>
        <h1 class="page-title">Categorías</h1>
        <p class="page-sub">Gestiona las categorías de productos</p>
    </div>
    <div class="head-actions">
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalCategoria"><i class="bi bi-plus-lg"></i> Nueva categoría</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar categoría…" class="input-brand" aria-label="Buscar categoría" />
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaCategorias" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Activa</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formCategoria" class="ajax-form" method="POST" action="{{ route('catalogo.categorias.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalCategoriaTitle">Nueva categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre <span class="req">*</span></label>
                        <input type="text" name="nombre" class="input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                    </div>
                    <div class="field">
                        <label class="switch">
                            <input type="checkbox" name="activa" value="1" checked>
                            <span class="slider"></span> Categoría activa
                        </label>
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
    initDataTable('tablaCategorias', '{{ route("catalogo.categorias.data") }}', [
        {data:'nombre',name:'nombre'},
        {data:'activa',name:'activa'},{data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
