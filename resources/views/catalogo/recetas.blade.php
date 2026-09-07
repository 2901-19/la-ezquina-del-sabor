@extends('layouts.app')
@section('title', 'Recetas - Catálogo')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Catálogo</p>
        <h1 class="page-title">Recetas</h1>
        <p class="page-sub">Gestiona las recetas de productos</p>
    </div>
    <div class="head-actions">
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalReceta"><i class="bi bi-plus-lg"></i> Nueva receta</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar receta o producto…" class="input-brand" aria-label="Buscar receta" />
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaRecetas" style="width:100%">
        <thead>
            <tr>
                <th>Receta</th>
                <th>Ingredientes</th>
                <th class="col-num">Costo USD</th>
                <th>Descripción</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Receta -->
<div class="modal fade" id="modalReceta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-surface">
            <form id="formReceta" class="ajax-form" method="POST" action="{{ route('catalogo.recetas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalRecetaTitle">Nueva receta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre <span class="req">*</span></label>
                        <input type="text" name="nombre" class="input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                    </div>
                    <div class="field">
                        <label class="label">Descripción</label>
                        <textarea name="descripcion" class="textarea-brand" rows="3" maxlength="500" placeholder="Describe la receta…"></textarea>
                    </div>
                    <div class="field">
                        <label class="label">Costo total (USD)</label>
                        <div class="input-money">
                            <span class="pre">$</span>
                            <input type="number" name="costo_total_usd" class="input-brand padx" step="0.01" min="0" value="0">
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
    initDataTable('tablaRecetas', '{{ route("catalogo.recetas.data") }}', [
        {data:'nombre',name:'nombre'},
        {data:'ingredientes_count',name:'ingredientes_count'},
        {data:'costo_total_usd',name:'costo_total_usd'},
        {data:'descripcion',name:'descripcion'},
        {data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
