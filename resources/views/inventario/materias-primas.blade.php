@extends('layouts.app')
@section('title', 'Materias Primas - Inventario')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Inventario</p>
        <h1 class="page-title">Materias Primas</h1>
        <p class="page-sub">Gestiona el inventario de materias primas</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost-brand" data-bs-toggle="modal" data-bs-target="#modalMerma"><i class="bi bi-trash3"></i> Registrar merma</button>
        <button class="btn-ghost-brand" data-bs-toggle="modal" data-bs-target="#modalCompra"><i class="bi bi-cart-plus"></i> Registrar compra</button>
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalMateriaPrima"><i class="bi bi-plus-lg"></i> Nueva materia prima</button>
    </div>
</div>

<div id="alertaCritica" style="display:none;" class="confirm-box">
    <i class="bi bi-exclamation-triangle"></i>
    <div>
        <div class="t">Stock crítico</div>
        <div class="s">X materia(s) con stock bajo el nivel mínimo</div>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar materia prima…" class="input-brand" aria-label="Buscar materia prima" />
    </div>
    <div class="filter-selects">
        <select id="filtroStock" class="select-brand" aria-label="Filtrar por stock">
            <option value="">Todo el stock</option>
            <option value="critico">Crítico</option>
            <option value="bajo">Stock bajo</option>
            <option value="optimo">Óptimo</option>
        </select>
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaMaterias" style="width:100%">
        <thead>
            <tr>
                <th>Materia prima</th>
                <th>Unidad</th>
                <th class="col-num">Existencias</th>
                <th class="col-num">Nivel mínimo</th>
                <th class="col-num">Costo últ. compra</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Materia Prima -->
<div class="modal fade" id="modalMateriaPrima" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formMateriaPrima" class="ajax-form" method="POST" action="{{ route('inventario.materias-primas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalMateriaPrimaTitle">Nueva materia prima</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Nombre <span class="req">*</span></label>
                        <input type="text" name="nombre" class="input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                    </div>
                    <div class="field">
                        <label class="label">Unidad de medida <span class="req">*</span></label>
                        <select name="unidad_medida" class="select-brand" required>
                            <option value="unidad">unidad</option>
                            <option value="g">g</option>
                            <option value="ml">ml</option>
                            <option value="kg">kg</option>
                            <option value="lb">lb</option>
                        </select>
                    </div>
                    <div class="field-2col">
                        <div class="field">
                            <label class="label">Stock actual <span class="req">*</span></label>
                            <input type="number" name="stock_actual" class="input-brand" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="field">
                            <label class="label">Stock mínimo <span class="req">*</span></label>
                            <input type="number" name="stock_minimo" class="input-brand" step="0.01" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Costo unitario (USD) <span class="req">*</span></label>
                        <div class="input-money">
                            <span class="pre">$</span>
                            <input type="number" name="costo_unitario_usd" class="input-brand padx" step="0.01" min="0" value="0" required>
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

<!-- Modal Registrar Compra -->
<div class="modal fade" id="modalCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formCompra" class="ajax-form" method="POST" action="{{ route('inventario.compras.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title">Registrar compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Materia prima <span class="req">*</span></label>
                        <select name="materia_prima_id" class="select-brand" required>
                            <option value="">Selecciona una materia prima</option>
                            @foreach($materiasPrimas as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->nombre }} ({{ $mp->unidad_medida }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-2col">
                        <div class="field">
                            <label class="label">Cantidad <span class="req">*</span></label>
                            <input type="number" name="cantidad" class="input-brand" step="0.01" min="0.01" required>
                        </div>
                        <div class="field">
                            <label class="label">Costo unitario (USD) <span class="req">*</span></label>
                            <div class="input-money">
                                <span class="pre">$</span>
                                <input type="number" name="costo_unitario_usd" class="input-brand padx" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Notas</label>
                        <textarea name="notas" class="textarea-brand" rows="2" maxlength="500" placeholder="Proveedor, lote, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Registrar compra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Merma -->
<div class="modal fade" id="modalMerma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <form id="formMerma" class="ajax-form" method="POST" action="{{ route('inventario.mermas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title">Registrar merma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label class="label">Materia prima <span class="req">*</span></label>
                        <select name="materia_prima_id" class="select-brand" required>
                            <option value="">Selecciona una materia prima</option>
                            @foreach($materiasPrimas as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->nombre }} ({{ $mp->unidad_medida }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="label">Cantidad perdida <span class="req">*</span></label>
                        <input type="number" name="cantidad" class="input-brand" step="0.01" min="0.01" required>
                    </div>
                    <div class="field">
                        <label class="label">Motivo <span class="req">*</span></label>
                        <select name="motivo" class="select-brand" required>
                            <option value="">Selecciona motivo</option>
                            <option value="desperdicio">Desperdicio</option>
                            <option value="deterioro">Deterioro</option>
                            <option value="caducidad">Caducidad</option>
                            <option value="error">Error de sistema</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="label">Notas</label>
                        <textarea name="notas" class="textarea-brand" rows="2" maxlength="500" placeholder="Detalle del incidente…"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Registrar merma</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaMaterias', '{{ route("inventario.materias-primas.data") }}', [
        {data:'nombre',name:'nombre'},{data:'unidad_medida',name:'unidad_medida'},
        {data:'stock_actual',name:'stock_actual'},{data:'stock_minimo',name:'stock_minimo'},
        {data:'costo_unitario_usd',name:'costo_unitario_usd'},{data:'acciones',name:'acciones',orderable:false,searchable:false}
    ], { '#filtroStock': 2 });
});
</script>
@endpush
