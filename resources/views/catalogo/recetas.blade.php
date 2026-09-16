@extends('layouts.app')
@section('title', 'Recetas - Catálogo')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Catálogo</p>
        <h1 class="page-title">Recetas</h1>
        <p class="page-sub">Gestiona las recetas y sus ingredientes</p>
    </div>
    <div class="head-actions">
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalReceta"><i class="bi bi-plus-lg"></i> Nueva receta</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar receta…" class="input-brand" aria-label="Buscar receta" />
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaRecetas" style="width:100%">
        <thead>
            <tr>
                <th>Receta</th>
                <th class="col-num">Ingredientes</th>
                <th class="col-num">Costo USD</th>
                <th>Descripción</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Receta -->
<div class="modal fade" id="modalReceta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-surface">
            <form id="formReceta" class="ajax-form" method="POST" action="{{ route('catalogo.recetas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalRecetaTitle">Nueva receta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Datos generales -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="field mb-0">
                                <label class="label">Nombre <span class="req">*</span></label>
                                <input type="text" name="nombre" class="input-brand" required minlength="2" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="field mb-0">
                                <label class="label">Descripción</label>
                                <input type="text" name="descripcion" class="input-brand" maxlength="500" placeholder="Describe la receta…">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="field mb-0">
                                <label class="label">Costo total calculado</label>
                                <div class="d-flex align-items-center justify-content-between px-3" style="height:calc(1.5em + 1rem + 2px);background:var(--surface-sunken);border:1px solid var(--border);border-radius:var(--radius-md);">
                                    <span class="text-muted" style="font-size:11px;">auto</span>
                                    <span class="fw-bold font-monospace" style="color:var(--accent);font-size:18px;" id="costoTotalDisplay">$ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ingredientes -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="label mb-0" style="font-size:13px;">Ingredientes</label>
                        <button type="button" class="btn-ghost-brand" onclick="agregarFila()" style="font-size:13px;padding:6px 12px;">
                            <i class="bi bi-plus-lg"></i> Agregar ingrediente
                        </button>
                    </div>

                    <div id="ingredientesContainer" style="border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;">
                        <table class="table table-sm align-middle mb-0" style="font-size:13px;">
                            <thead>
                                <tr style="background:var(--surface-sunken);">
                                    <th style="width:140px;">Tipo</th>
                                    <th>Material</th>
                                    <th style="width:120px;">Cantidad</th>
                                    <th style="width:120px;">Costo</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="ingredientesBody">
                            </tbody>
                        </table>
                        <div id="emptyState" class="text-center py-4 text-muted" style="font-size:13px;">
                            <i class="bi bi-info-circle"></i> Agrega ingredientes para calcular el costo de la receta
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Guardar receta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Receta -->
<div class="modal fade" id="modalVerReceta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="verRecetaTitle">Detalle de receta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="field mb-0">
                            <label class="label">Nombre</label>
                            <div class="input-brand" id="verRecetaNombre" style="border:none;background:transparent;padding-top:7px;">—</div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="field mb-0">
                            <label class="label">Descripción</label>
                            <div class="input-brand" id="verRecetaDescripcion" style="border:none;background:transparent;padding-top:7px;">—</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="field mb-0">
                            <label class="label">Costo total</label>
                            <div class="d-flex align-items-center justify-content-between px-3" style="height:calc(1.5em + 1rem + 2px);background:var(--surface-sunken);border:1px solid var(--border);border-radius:var(--radius-md);">
                                <span class="text-muted" style="font-size:11px;">USD</span>
                                <span class="fw-bold font-monospace" style="color:var(--accent);font-size:18px;" id="verRecetaCosto">$ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <label class="label" style="font-size:13px;">Ingredientes</label>
                <div style="border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;">
                    <table class="table table-sm align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="background:var(--surface-sunken);">
                                <th>Tipo</th>
                                <th>Material</th>
                                <th style="width:120px;">Cantidad</th>
                                <th style="width:120px;" class="text-end">Costo</th>
                            </tr>
                        </thead>
                        <tbody id="verRecetaIngredientes">
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin ingredientes</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-primary-brand" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var MATERIAS_PRIMAS = @json($materiasPrimas->mapWithKeys(fn($mp) => [$mp->id => ['nombre' => $mp->nombre, 'costo' => (float)$mp->costo_unitario_usd, 'unidad' => $mp->unidad_medida]]));
var RECETAS_DISPONIBLES = @json($recetas->mapWithKeys(fn($r) => [$r->id => ['nombre' => $r->nombre, 'costo' => (float)$r->costo_total_usd]]));
var indiceFila = 0;

function agregarFila(materiaPrimaId, recetaBaseId, cantidad, detalleId) {
    indiceFila++;
    var tbody = document.getElementById('ingredientesBody');
    var tr = document.createElement('tr');
    tr.className = 'ingrediente-row';
    tr.setAttribute('data-index', indiceFila);

    var hiddenId = detalleId ? '<input type="hidden" name="detalles[' + indiceFila + '][id]" value="' + detalleId + '">' : '';

    var optionsMp = '<option value="">Seleccionar…</option>';
    Object.keys(MATERIAS_PRIMAS).forEach(function(id) {
        var mp = MATERIAS_PRIMAS[id];
        var selected = (materiaPrimaId && materiaPrimaId == id) ? ' selected' : '';
        optionsMp += '<option value="' + id + '"' + selected + '>' + mp.nombre + ' ($' + mp.costo.toFixed(2) + '/' + mp.unidad + ')</option>';
    });

    var optionsSr = '<option value="">Seleccionar…</option>';
    Object.keys(RECETAS_DISPONIBLES).forEach(function(id) {
        var r = RECETAS_DISPONIBLES[id];
        var recetaActual = document.querySelector('#formReceta [name="nombre"]');
        var selected = (recetaBaseId && recetaBaseId == id) ? ' selected' : '';
        optionsSr += '<option value="' + id + '"' + selected + '>' + r.nombre + ' ($' + r.costo.toFixed(2) + ')</option>';
    });

    var tipoMp = (!recetaBaseId || recetaBaseId === '') ? '' : 'none';
    var tipoSr = (recetaBaseId && recetaBaseId !== '') ? '' : 'none';
    var cant = cantidad || 1;

    tr.innerHTML =
        '<td>' + hiddenId +
            '<select class="input-brand input-tipo" style="font-size:13px;padding:6px 8px;" onchange="cambiarTipo(this)">' +
                '<option value="materia_prima"' + (tipoMp !== 'none' ? ' selected' : '') + '>Materia Prima</option>' +
                '<option value="sub_receta"' + (tipoSr !== 'none' ? ' selected' : '') + '>Sub-receta</option>' +
            '</select>' +
        '</td>' +
        '<td>' +
            '<select class="input-brand input-mp" name="detalles[' + indiceFila + '][materia_prima_id]" style="font-size:13px;padding:6px 8px;display:' + tipoMp + ';" onchange="calcularCostoFila(this)">' + optionsMp + '</select>' +
            '<select class="input-brand input-sr" name="detalles[' + indiceFila + '][receta_base_id]" style="font-size:13px;padding:6px 8px;display:' + tipoSr + ';" onchange="calcularCostoFila(this)">' + optionsSr + '</select>' +
        '</td>' +
        '<td>' +
            '<input type="number" name="detalles[' + indiceFila + '][cantidad_requerida]" class="input-brand" style="font-size:13px;padding:6px 8px;" step="0.01" min="0.01" value="' + cant + '" onchange="calcularCostoFila(this)" oninput="calcularCostoFila(this)">' +
        '</td>' +
        '<td class="costo-fila fw-bold font-monospace" style="color:var(--accent);">$0.00</td>' +
        '<td>' +
            '<button type="button" class="icon-btn del" onclick="eliminarFila(this)" title="Eliminar">' +
                '<i class="bi bi-trash3"></i>' +
            '</button>' +
        '</td>';

    tbody.appendChild(tr);
    calcularCostoFila(tr.querySelector('.input-mp'));
    actualizarEmptyState();
}

function eliminarFila(btn) {
    btn.closest('tr').remove();
    recalcularCostoTotal();
    actualizarEmptyState();
}

function cambiarTipo(select) {
    var tr = select.closest('tr');
    var mpSelect = tr.querySelector('.input-mp');
    var srSelect = tr.querySelector('.input-sr');

    if (select.value === 'materia_prima') {
        mpSelect.style.display = '';
        mpSelect.name = 'detalles[' + tr.getAttribute('data-index') + '][materia_prima_id]';
        srSelect.style.display = 'none';
        srSelect.name = '';
        srSelect.value = '';
    } else {
        mpSelect.style.display = 'none';
        mpSelect.name = '';
        mpSelect.value = '';
        srSelect.style.display = '';
        srSelect.name = 'detalles[' + tr.getAttribute('data-index') + '][receta_base_id]';
    }

    calcularCostoFila(select);
}

function calcularCostoFila(el) {
    var tr = el.closest('tr');
    var tipo = tr.querySelector('.input-tipo').value;
    var materialId, costoUnitario = 0;

    if (tipo === 'materia_prima') {
        materialId = tr.querySelector('.input-mp').value;
        if (materialId && MATERIAS_PRIMAS[materialId]) {
            costoUnitario = MATERIAS_PRIMAS[materialId].costo;
        }
    } else {
        materialId = tr.querySelector('.input-sr').value;
        if (materialId && RECETAS_DISPONIBLES[materialId]) {
            costoUnitario = RECETAS_DISPONIBLES[materialId].costo;
        }
    }

    var cantidad = parseFloat(tr.querySelector('[name*="cantidad_requerida"]').value) || 0;
    var costo = costoUnitario * cantidad;
    tr.querySelector('.costo-fila').textContent = '$ ' + costo.toFixed(2);
    recalcularCostoTotal();
}

function recalcularCostoTotal() {
    var total = 0;
    document.querySelectorAll('.costo-fila').forEach(function(td) {
        var val = parseFloat(td.textContent.replace('$', '').replace(',', '').trim()) || 0;
        total += val;
    });
    document.getElementById('costoTotalDisplay').textContent = '$ ' + total.toFixed(2);
}

function actualizarEmptyState() {
    var rows = document.querySelectorAll('.ingrediente-row');
    var empty = document.getElementById('emptyState');
    if (empty) empty.style.display = rows.length === 0 ? '' : 'none';
}

function cargarDetalleReceta(record) {
    var tbody = document.getElementById('ingredientesBody');
    if (tbody) tbody.innerHTML = '';
    indiceFila = 0;

    if (record.receta_detalles && record.receta_detalles.length) {
        record.receta_detalles.forEach(function(d) {
            agregarFila(d.materia_prima_id, d.receta_base_id, d.cantidad_requerida, d.id);
        });
    }

    recalcularCostoTotal();
    actualizarEmptyState();
}
window.cargarDetalleReceta = cargarDetalleReceta;

document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaRecetas', '{{ route("catalogo.recetas.data") }}', [
        {data:'nombre', name:'nombre'},
        {data:'ingredientes_count', name:'ingredientes_count', className:'text-center'},
        {data:'costo_formateado', name:'costo_total_usd', className:'text-end font-monospace'},
        {data:'descripcion', name:'descripcion'},
        {data:'acciones', name:'acciones', orderable:false, searchable:false, className:'text-end'}
    ]);

    document.getElementById('modalReceta').addEventListener('show.bs.modal', function() {
        var form = this.querySelector('.ajax-form');
        var isEdit = /\d+$/.test(form.getAttribute('action'));
        if (!isEdit) {
            var tbody = document.getElementById('ingredientesBody');
            if (tbody) tbody.innerHTML = '';
            indiceFila = 0;
            document.getElementById('costoTotalDisplay').textContent = '$ 0.00';
            actualizarEmptyState();
        }
    });

    var formReceta = document.getElementById('formReceta');
    if (formReceta) {
        formReceta.addEventListener('submit', function() {
            this.querySelectorAll('.ingrediente-row').forEach(function(row) {
                var mpSelect = row.querySelector('.input-mp');
                var srSelect = row.querySelector('.input-sr');
                if (mpSelect && mpSelect.style.display === 'none') mpSelect.removeAttribute('name');
                if (srSelect && srSelect.style.display === 'none') srSelect.removeAttribute('name');
            });
        }, true);
    }

    document.addEventListener('click', function(e) {
        var verBtn = e.target.closest('[data-act="ver-receta"]');
        if (!verBtn) return;
        e.preventDefault();

        var showUrl = verBtn.getAttribute('data-url');
        if (!showUrl) return;

        fetch(showUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success || !data.data) { showToast('Error al cargar la receta', 'error'); return; }
            var receta = data.data;

            document.getElementById('verRecetaNombre').textContent = receta.nombre || '—';
            document.getElementById('verRecetaDescripcion').textContent = receta.descripcion || '—';
            document.getElementById('verRecetaCosto').textContent = '$ ' + Number(receta.costo_total_usd || 0).toFixed(2);

            var tbody = document.getElementById('verRecetaIngredientes');
            tbody.innerHTML = '';
            var detalles = receta.receta_detalles || [];
            if (!detalles.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Sin ingredientes</td></tr>';
            } else {
                detalles.forEach(function(d) {
                    var tr = document.createElement('tr');
                    var tipo, material, costo;

                    if (d.materia_prima_id && d.materia_prima) {
                        tipo = 'Materia Prima';
                        material = d.materia_prima.nombre;
                        costo = Number(d.materia_prima.costo_unitario_usd || 0) * Number(d.cantidad_requerida || 0);
                    } else if (d.receta_base_id && d.receta_base) {
                        tipo = 'Sub-receta';
                        material = d.receta_base.nombre;
                        costo = Number(d.receta_base.costo_total_usd || 0) * Number(d.cantidad_requerida || 0);
                    } else {
                        tipo = '—';
                        material = '—';
                        costo = 0;
                    }

                    tr.innerHTML =
                        '<td>' + tipo + '</td>' +
                        '<td>' + material + '</td>' +
                        '<td>' + Number(d.cantidad_requerida || 0) + '</td>' +
                        '<td class="text-end font-monospace">$ ' + costo.toFixed(2) + '</td>';
                    tbody.appendChild(tr);
                });
            }

            openModal('modalVerReceta');
        })
        .catch(function() { showToast('Error al cargar la receta', 'error'); });
    });
});
</script>
@endpush
