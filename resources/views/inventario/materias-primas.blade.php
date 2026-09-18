@extends('layouts.app')
@section('title', 'Inventario')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Inventario</p>
        <h1 class="page-title">Inventario</h1>
        <p class="page-sub">Stock de ingredientes, compras y mermas</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost-brand" data-bs-toggle="modal" data-bs-target="#modalMerma"><i class="bi bi-trash3"></i> Registrar merma</button>
        <button class="btn-ghost-brand" data-bs-toggle="modal" data-bs-target="#modalCompra"><i class="bi bi-cart-plus"></i> Registrar compra</button>
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalMateriaPrima"><i class="bi bi-plus-lg"></i> Nueva materia prima</button>
    </div>
</div>

@if($criticas > 0 || $bajas > 0)
<div class="confirm-box" style="margin-bottom:12px;">
    <i class="bi bi-exclamation-triangle" style="color:var(--danger);"></i>
    <div>
        <div class="t">Alerta de stock</div>
        <div class="s">
            @if($criticas > 0)<span style="color:var(--danger);font-weight:600;">{{ $criticas }}</span> en estado crítico @endif
            @if($criticas > 0 && $bajas > 0) — @endif
            @if($bajas > 0)<span style="color:var(--warn);font-weight:600;">{{ $bajas }}</span> con stock bajo @endif
        </div>
    </div>
</div>
@endif

<ul class="browser-tabs" id="tabsInventario" role="tablist">
    <li class="browser-tab"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabMaterias" type="button"><i class="bi bi-box-seam"></i> Materias primas</button></li>
    <li class="browser-tab"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCompras" type="button"><i class="bi bi-cart-plus"></i> Compras</button></li>
    <li class="browser-tab"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMermas" type="button"><i class="bi bi-exclamation-triangle"></i> Mermas</button></li>
    <li class="browser-tab"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMovimientos" type="button"><i class="bi bi-clock-history"></i> Movimientos</button></li>
</ul>

<div class="tab-content" style="margin-top:2px;">
    <!-- Materias primas -->
    <div class="tab-pane fade show active" id="tabMaterias" role="tabpanel">
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
                        <th class="col-num">Estado</th>
                        <th class="col-num">Existencias</th>
                        <th class="col-num">Nivel mínimo</th>
                        <th class="col-num">Costo últ. compra</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Compras -->
    <div class="tab-pane fade" id="tabCompras" role="tabpanel">
        <div class="table-panel">
            <table class="table display" id="tablaCompras" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Materia prima</th>
                        <th class="col-num">Total</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Mermas -->
    <div class="tab-pane fade" id="tabMermas" role="tabpanel">
        <div class="table-panel">
            <table class="table display" id="tablaMermas" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Materia prima</th>
                        <th class="col-num">Cantidad</th>
                        <th>Motivo</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Movimientos -->
    <div class="tab-pane fade" id="tabMovimientos" role="tabpanel">
        <div class="filter-card">
            <div class="filter-selects">
                <select id="filtroMovMP" class="select-brand" aria-label="Filtrar por materia prima">
                    <option value="">Todas las materias primas</option>
                    @foreach($materiasPrimas as $mp)
                    <option value="{{ $mp->id }}">{{ $mp->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="table-panel">
            <table class="table display" id="tablaMovimientos" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Materia prima</th>
                        <th>Tipo</th>
                        <th class="col-num">Cantidad</th>
                        <th class="col-num">Costo unit.</th>
                        <th>Nota</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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

<!-- Modal Ver Materia Prima -->
<div class="modal fade" id="modalVerMateria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="verMateriaTitle">Detalle de materia prima</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verMateriaBody"></div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
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
                        <select name="materia_prima_id" id="compraMateriaPrima" class="select-brand" required>
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
                        <label class="label">Fecha de compra</label>
                        <input type="date" name="fecha_compra" class="input-brand" value="{{ date('Y-m-d') }}">
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
                        <select name="materia_prima_id" id="mermaMateriaPrima" class="select-brand" required>
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

<!-- Modal Ver Compra -->
<div class="modal fade" id="modalVerCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="verCompraTitle">Detalle de compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verCompraBody"></div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Merma -->
<div class="modal fade" id="modalVerMerma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="verMermaTitle">Detalle de merma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verMermaBody"></div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Movimientos de una MP -->
<div class="modal fade" id="modalMovimientos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="modalMovTitle">Movimientos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm align-middle mb-0" style="font-size:13px;">
                    <thead><tr style="background:var(--surface-sunken);"><th>Fecha</th><th>Tipo</th><th class="col-num">Cantidad</th><th class="col-num">Costo</th><th>Nota</th></tr></thead>
                    <tbody id="movimientosBody"><tr><td colspan="5" class="text-center text-muted py-3">Cargando…</td></tr></tbody>
                </table>
            </div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var MATERIAS_MP = @json($materiasPrimas->mapWithKeys(fn($mp) => [$mp->id => $mp->nombre]));
var activaTabla = null;

document.addEventListener('DOMContentLoaded', function() {
    activaTabla = initInventarioDataTable();

    // Cambio de pestaña: re-init datatable visible
    document.querySelectorAll('#tabsInventario .nav-link').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function() {
            var target = this.getAttribute('data-bs-target');
            if (target === '#tabMaterias' || !target) return;
        });
    });

    // Búsqueda: aplica a la tabla de la pestaña activa
    var buscarInput = document.getElementById('buscar');
    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            if (activaTabla) activaTabla.search(this.value).draw();
        });
    }
});

function initInventarioDataTable() {
    // Materias primas
    var matTable = $('#tablaMaterias').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '{{ route("inventario.materias-primas.data") }}',
            data: function(d) {
                var f = document.getElementById('filtroStock');
                if (f && f.value) d.estado_stock = f.value;
            }
        },
        columns: [
            {data:'nombre', name:'nombre'},
            {data:'unidad_medida', name:'unidad_medida'},
            {data:'estado_stock', name:'estado_stock', orderable:false, searchable:false,
                render: function(d) {
                    var cls = {critico:'badge-merma', bajo:'badge-salida', optimo:'badge-entrada'};
                    var lbl = {critico:'Crítico', bajo:'Bajo', optimo:'Óptimo'};
                    return '<span class="badge-mov '+(cls[d]||'')+'">'+(lbl[d]||d)+'</span>';
                }
            },
            {data:'stock_actual', name:'stock_actual', className:'col-num'},
            {data:'stock_minimo', name:'stock_minimo', className:'col-num'},
            {data:'costo_unitario_usd', name:'costo_unitario_usd', className:'col-num'},
            {data:'acciones', name:'acciones', orderable:false, searchable:false, className:'text-end'}
        ],
        dom: '<"row mb-2"<"col-sm-6"l>>rt<"row dt-footer"<"col-sm-6"i><"col-sm-6 text-end"p>>',
        pageLength: 10,
        language: {
            search: '', lengthMenu: 'Mostrar _MENU_', info: 'Mostrando _START_ a _END_ de _TOTAL_',
            zeroRecords: 'Sin resultados', loadingRecords: 'Cargando...', paginate:{first:'<i class="bi bi-chevron-double-left"></i>',last:'<i class="bi bi-chevron-double-right"></i>',next:'<i class="bi bi-chevron-right"></i>',previous:'<i class="bi bi-chevron-left"></i>'}
        }
    });
    $('#filtroStock').on('change', function() { matTable.ajax.reload(); });

    // Compras
    $('#tablaCompras').DataTable({
        processing: true, serverSide: true,
        ajax: '{{ route("inventario.compras.data") }}',
        columns: [
            {data:'fecha_compra', name:'fecha_compra'},
            {data:'materia_prima', name:'materia_prima', orderable:false},
            {data:'total', name:'total', className:'col-num'},
            {data:'acciones', name:'acciones', orderable:false, searchable:false, className:'text-end'}
        ],
        dom: '<"row mb-2"<"col-sm-6"l>>rt<"row dt-footer"<"col-sm-6"i><"col-sm-6 text-end"p>>',
        pageLength: 10,
        language: {search:'', zeroRecords:'Sin compras registradas.', loadingRecords:'Cargando...', paginate:{first:'<i class="bi bi-chevron-double-left"></i>',last:'<i class="bi bi-chevron-double-right"></i>',next:'<i class="bi bi-chevron-right"></i>',previous:'<i class="bi bi-chevron-left"></i>'}}
    });

    // Mermas
    $('#tablaMermas').DataTable({
        processing: true, serverSide: true,
        ajax: '{{ route("inventario.mermas.data") }}',
        columns: [
            {data:'fecha', name:'fecha', orderable:false},
            {data:'materia_prima_nombre', name:'materia_prima_nombre'},
            {data:'cantidad_movimiento', name:'cantidad_movimiento', className:'col-num'},
            {data:'nota', name:'nota', orderable:false, searchable:false},
            {data:'acciones', name:'acciones', orderable:false, searchable:false, className:'text-end'}
        ],
        dom: '<"row mb-2"<"col-sm-6"l>>rt<"row dt-footer"<"col-sm-6"i><"col-sm-6 text-end"p>>',
        pageLength: 10,
        language: {search:'', zeroRecords:'Sin mermas registradas.', loadingRecords:'Cargando...', paginate:{first:'<i class="bi bi-chevron-double-left"></i>',last:'<i class="bi bi-chevron-double-right"></i>',next:'<i class="bi bi-chevron-right"></i>',previous:'<i class="bi bi-chevron-left"></i>'}}
    });

    // Movimientos
    var movUrl = '{{ route("inventario.movimientos.data") }}';
    $('#tablaMovimientos').DataTable({
        processing: true, serverSide: true,
        ajax: {url: movUrl, data: function(d) {
            var mp = document.getElementById('filtroMovMP');
            if (mp && mp.value) d.materia_prima_id = mp.value;
        }},
        columns: [
            {data:'fecha_movimiento', name:'fecha_movimiento'},
            {data:'materia_prima', name:'materia_prima'},
            {data:'tipo_label', name:'tipo_movimiento', orderable:false},
            {data:'cantidad_movimiento', name:'cantidad_movimiento', className:'col-num'},
            {data:'costo_unitario', name:'costo_unitario', className:'col-num'},
            {data:'nota', name:'nota', orderable:false, searchable:false}
        ],
        dom: '<"row mb-2"<"col-sm-6"l>>rt<"row dt-footer"<"col-sm-6"i><"col-sm-6 text-end"p>>',
        pageLength: 10,
        language: {search:'', zeroRecords:'Sin movimientos.', loadingRecords:'Cargando...', paginate:{first:'<i class="bi bi-chevron-double-left"></i>',last:'<i class="bi bi-chevron-double-right"></i>',next:'<i class="bi bi-chevron-right"></i>',previous:'<i class="bi bi-chevron-left"></i>'}}
    });
    $('#filtroMovMP').on('change', function() { $('#tablaMovimientos').DataTable().ajax.reload(); });
}

// Handlers de acciones en la tabla
document.addEventListener('click', function(e) {
    // Ver detalle materia prima
    var verMp = e.target.closest('[data-act="ver-materia"]');
    if (verMp) {
        e.preventDefault();
        var mpId = verMp.getAttribute('data-id');
        fetch('/inventario/materias-primas/' + mpId, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var mp = res.data;
            var estados = {critico:'Crítico', bajo:'Bajo', optimo:'Óptimo'};
            var cls = {critico:'badge-merma', bajo:'badge-salida', optimo:'badge-entrada'};
            var html = '<div class="row g-3">';
            html += '<div class="col-md-12"><div class="text-muted" style="font-size:12px">Nombre</div><div class="fw-bold fs-5">'+(mp.nombre || '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Unidad de medida</div><div class="fw-bold">'+(mp.unidad_medida || '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Estado del stock</div><div><span class="badge-mov '+(cls[mp.estado_stock]||'')+'">'+(estados[mp.estado_stock]||mp.estado_stock||'—')+'</span></div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Existencias</div><div class="fw-bold stock '+(mp.estado_stock||'')+'">'+Number(mp.stock_actual).toFixed(2)+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Nivel mínimo</div><div class="fw-bold">'+Number(mp.stock_minimo).toFixed(2)+'</div></div>';
            html += '<div class="col-md-12"><div class="text-muted" style="font-size:12px">Costo unitario (USD)</div><div class="fw-bold font-monospace">$ '+Number(mp.costo_unitario_usd).toFixed(2)+'</div></div>';
            html += '</div>';
            document.getElementById('verMateriaTitle').textContent = 'Detalle de materia prima';
            document.getElementById('verMateriaBody').innerHTML = html;
            openModal('modalVerMateria');
        })
        .catch(function() { showToast('Error al cargar la materia prima', 'error'); });
        return;
    }

    // Movimientos por MP (desde tabla materia prima)
    var movBtn = e.target.closest('[data-act="movimientos"]');
    if (movBtn) {
        e.preventDefault();
        var mpId = movBtn.getAttribute('data-id');
        var mpNombre = movBtn.getAttribute('data-nombre') || 'MP #' + mpId;
        document.getElementById('modalMovTitle').textContent = 'Movimientos — ' + mpNombre;
        var tbody = document.getElementById('movimientosBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Cargando…</td></tr>';
        fetch('/inventario/movimientos/data?materia_prima_id=' + mpId, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var data = res.data || [];
            if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Sin movimientos para esta materia prima.</td></tr>'; return; }
            var mapaTipo = {entrada:'Entrada',salida:'Salida',merma:'Merma'};
            var cls = {entrada:'badge-entrada',salida:'badge-salida',merma:'badge-merma'};
            var rows = data.map(function(m) {
                return '<tr><td>'+m.fecha_movimiento+'</td><td><span class="badge-mov '+(cls[m.tipo_movimiento]||'')+'">'+(mapaTipo[m.tipo_movimiento]||m.tipo_movimiento)+'</span></td><td class="col-num"><span class="stock '+m.tipo_movimiento+'">'+Number(m.cantidad_movimiento).toFixed(2)+'</span></td><td class="col-num">$ '+Number(m.costo_unitario).toFixed(2)+'</td><td>'+(m.nota||'')+'</td></tr>';
            }).join('');
            tbody.innerHTML = rows;
        })
        .catch(function() { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Error al cargar movimientos.</td></tr>'; });
        openModal('modalMovimientos');
        return;
    }

    // Ver detalle compra
    var verCompra = e.target.closest('[data-act="ver-compra"]');
    if (verCompra) {
        e.preventDefault();
        var cId = verCompra.getAttribute('data-id');
        fetch('/inventario/compras/' + cId, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var c = res.data;
            var det = c.compra_detalles || [];
            var rows = det.map(function(d) {
                return '<tr><td>'+(d.materia_prima ? d.materia_prima.nombre : d.materia_prima_id)+'</td><td class="col-num">'+Number(d.cantidad).toFixed(2)+'</td><td class="col-num">$ '+Number(d.costo_unitario).toFixed(2)+'</td><td class="col-num">$ '+Number(d.costo_total).toFixed(2)+'</td></tr>';
            }).join('');
            var html = '<div class="mb-3"><div class="text-muted" style="font-size:12px">Fecha</div><div class="fw-bold">'+(c.fecha_compra || '—')+'</div></div>';
            if (c.referencia) html += '<div class="mb-3"><div class="text-muted" style="font-size:12px">Referencia</div><div>'+c.referencia+'</div></div>';
            html += '<table class="table table-sm align-middle mb-0"><thead><tr><th>Materia prima</th><th class="col-num">Cantidad</th><th class="col-num">Costo unit.</th><th class="col-num">Subtotal</th></tr></thead><tbody>'+(rows||'<tr><td colspan="4" class="text-center text-muted py-3">Sin detalles</td></tr>')+'</tbody></table>';
            html += '<div class="text-end fw-bold" style="font-size:16px;color:var(--accent);">Total: $ '+Number(c.total).toFixed(2)+'</div>';
            document.getElementById('verCompraTitle').textContent = 'Compra #'+c.id;
            document.getElementById('verCompraBody').innerHTML = html;
            openModal('modalVerCompra');
        })
        .catch(function() { showToast('Error al cargar compra', 'error'); });
        return;
    }

    // Ver detalle merma
    var verMerma = e.target.closest('[data-act="ver-merma"]');
    if (verMerma) {
        e.preventDefault();
        var mId = verMerma.getAttribute('data-id');
        fetch('/inventario/mermas/' + mId, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var m = res.data;
            var html = '<div class="row g-3">';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Fecha</div><div class="fw-bold">'+(m.fecha_movimiento || '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Materia prima</div><div class="fw-bold">'+(m.materia_prima ? m.materia_prima.nombre : '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Cantidad perdida</div><div class="fw-bold stock merma">'+Number(m.cantidad_movimiento).toFixed(2)+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Costo unitario</div><div class="fw-bold font-monospace">$ '+Number(m.costo_unitario).toFixed(2)+'</div></div>';
            if (m.nota) html += '<div class="col-md-12"><div class="text-muted" style="font-size:12px">Detalle</div><div>'+m.nota+'</div></div>';
            html += '</div>';
            document.getElementById('verMermaTitle').textContent = 'Detalle de merma';
            document.getElementById('verMermaBody').innerHTML = html;
            openModal('modalVerMerma');
        })
        .catch(function() { showToast('Error al cargar merma', 'error'); });
        return;
    }
});

// Reset forms al abrir modal
document.querySelectorAll('[data-bs-target]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.getAttribute('data-bs-target');
        if (!id) return;
        var modal = document.querySelector(id);
        if (!modal) return;
        var form = modal.querySelector('.ajax-form');
        if (form) {
            form.reset();
            var m = form.querySelector('[name="_method"]');
            if (m) m.value = 'POST';
            form.setAttribute('action', form.getAttribute('action').replace(/\/\d+$/, ''));
        }
    });
});
</script>
@endpush
