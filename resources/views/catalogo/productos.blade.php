@extends('layouts.app')
@section('title', 'Productos - Catálogo')
@section('content')
<div class="page-head">
    <div>
        <p class="page-eyebrow">Módulo catálogo · Lista de productos</p>
        <h1 class="page-title">Catálogo de productos</h1>
        <p class="page-sub">Menú completo: precios en USD y Bs, estado y receta vinculada.</p>
    </div>
    <div class="head-actions">
        <a href="{{ route('catalogo.combos.index') }}" class="btn-ghost-brand"><i class="bi bi-collection"></i> Combos</a>
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalProducto"><i class="bi bi-plus-lg"></i> Nuevo producto</button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar producto…" class="input-brand" aria-label="Buscar producto" />
    </div>
    <div class="filter-selects">
        <select id="filtroCat" class="select-brand" aria-label="Filtrar por categoría">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
            <option value="{{ $cat->nombre }}">{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <select id="filtroEstado" class="select-brand" aria-label="Filtrar por estado">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>
    </div>
    <span class="filter-count" id="contador"></span>
</div>

<div class="table-panel">
    <table class="table display" id="tablaProductos" style="width:100%">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Tipo de precio</th>
                <th class="col-num">Precio USD</th>
                <th class="col-num">Precio Bs</th>
                <th>Estado</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-surface">
            <form id="formProducto" class="ajax-form" method="POST" action="{{ route('catalogo.productos.store') }}">
                @csrf
                <input type="hidden" name="tipo_precio" id="fTipoPrecio" value="margen">
                <input type="hidden" name="precio_usd" id="fPrecioUsd" value="0">
                <input type="hidden" name="_method" id="fMethod" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalProductoTitle">Nuevo producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid">
                        <div>
                            <div class="panel" style="margin-bottom:20px;">
                                <div class="panel-head"><div class="panel-title">Datos del producto</div></div>
                                <div class="panel-body">
                                    <div class="field">
                                        <label class="label">Nombre <span class="req">*</span></label>
                                        <input type="text" name="nombre" id="fNombre" class="input-brand" placeholder="Ej. Hamburguesa Esquina" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                                    </div>
                                    <div class="field">
                                        <label class="label">Categoría <span class="req">*</span></label>
                                        <select name="categoria_id" id="fCategoria" class="select-brand" required>
                                            <option value="">Selecciona categoría</option>
                                            @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-head"><div class="panel-title">Cómo calcular el precio</div></div>
                                <div class="panel-body">
                                    <div class="field">
                                        <div class="seg">
                                            <button type="button" class="active" id="tipoMargen" onclick="setTipoPrecio('margen')"><i class="bi bi-percent"></i> Margen</button>
                                            <button type="button" id="tipoDefinido" onclick="setTipoPrecio('definido')"><i class="bi bi-tag"></i> Definido</button>
                                        </div>
                                    </div>
                                    <div id="bloqueMargen">
                                        <div class="field">
                                            <label class="switch" id="wrapIndexarCosto" style="display:none;">
                                                <input type="checkbox" name="indexar_costo_receta" id="fIndexarCosto" value="1">
                                                <span class="slider"></span> Usar costo de la receta
                                            </label>
                                        </div>
                                        <div class="field-2col">
                                            <div class="field">
                                                <label class="label">Costo (USD)</label>
                                                <div class="input-money">
                                                    <span class="pre">$</span>
                                                    <input type="number" name="costo_usd" id="fCosto" class="input-brand padx" step="0.01" min="0" value="0">
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Ganancia %</label>
                                                <div class="input-margin">
                                                    <input type="number" name="margen_ganancia" id="fMargen" class="input-brand padx-r" step="0.01" min="0" max="200" value="35">
                                                    <span class="post">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="calc-box">
                                            <div class="calc-row"><span class="k">Costo:</span><span id="cCosto" class="v-usd">$ 0,00</span></div>
                                            <div class="calc-row"><span class="k">Ganancia:</span><span id="cGanancia" class="v-usd">$ 0,00</span></div>
                                            <div class="calc-row precio"><span class="k">Precio:</span><span id="cPrecio" class="v-usd">$ 0,00</span></div>
                                            <div class="calc-bs accent" id="cPrecioBs">Bs 0,00</div>
                                        </div>
                                    </div>
                                    <div id="bloqueDefinido" style="display:none;">
                                        <div class="field">
                                            <label class="label">Precio fijo (USD)</label>
                                            <div class="input-money">
                                                <span class="pre">$</span>
                                                <input type="number" name="precio_def" id="fPrecioDef" class="input-brand padx" step="0.01" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="panel" style="margin-bottom:20px;">
                                <div class="panel-head"><div class="panel-title">Receta y estado</div></div>
                                <div class="panel-body">
                                    <div class="field">
                                        <label class="label">Vínculo a receta</label>
                                        <select name="receta_id" id="fReceta" class="select-brand">
                                            <option value="">Sin receta vinculada</option>
                                            @foreach($recetas as $r)
                                            <option value="{{ $r->id }}" data-costo="{{ $r->costo_total_usd }}">{{ $r->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="switch">
                                            <input type="checkbox" name="activo" id="fActivo" value="1" checked>
                                            <span class="slider"></span> Activo / Visible y vendible
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="side-panel panel" style="position:sticky; top:80px;">
                                <div class="panel-body" style="text-align:center;">
                                    <div class="side-label">Resumen de precio</div>
                                    <div id="resUsd" style="font-family:var(--font-display); font-size:24px; font-weight:700;">$ 0,00</div>
                                    <div id="resBs" style="font-family:var(--font-mono); font-size:14px; color:var(--accent);">Bs 0,00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-brand">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand" id="btnGuardar">Guardar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Producto -->
<div class="modal fade" id="modalVerProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-surface">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title" id="verProductoTitle">Detalle de producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verProductoBody"></div>
            <div class="modal-footer modal-footer-brand">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
window.cargarDetalleProducto = function(record) {
    if (!record.tipo_precio) return;
    setTipoPrecio(record.tipo_precio);
    var chk = document.getElementById('fIndexarCosto');
    if (chk) {
        chk.setAttribute('data-manual', '1');
    }
    var costo = document.getElementById('fCosto');
    var margen = document.getElementById('fMargen');
    if (costo && margen) {
        costo.dispatchEvent(new Event('input'));
        margen.dispatchEvent(new Event('input'));
    }
    actualizarVinculoReceta();
};

function actualizarVinculoReceta() {
    var select = document.getElementById('fReceta');
    var wrap = document.getElementById('wrapIndexarCosto');
    var chk = document.getElementById('fIndexarCosto');
    if (!select || !wrap || !chk) return;

    var tipo = document.getElementById('fTipoPrecio');
    var conReceta = !!select.value && (tipo ? tipo.value === 'margen' : true);
    wrap.style.display = conReceta ? 'block' : 'none';

    if (!conReceta) {
        chk.checked = false;
        setCostoReceta(false);
        return;
    }

    if (!chk.hasAttribute('data-manual')) {
        chk.checked = true;
    }
    setCostoReceta(chk.checked);
}

function setCostoReceta(usarReceta) {
    var select = document.getElementById('fReceta');
    var chk = document.getElementById('fIndexarCosto');
    var costo = document.getElementById('fCosto');
    if (!select || !chk || !costo) return;
    costo.disabled = usarReceta;
    if (usarReceta) {
        var opcion = select.options[select.selectedIndex];
        var valor = opcion ? parseFloat(opcion.getAttribute('data-costo')) || 0 : 0;
        costo.value = valor.toFixed(2);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('fReceta');
    var chk = document.getElementById('fIndexarCosto');
    if (select) select.addEventListener('change', function() {
        chk.removeAttribute('data-manual');
        actualizarVinculoReceta();
    });
    if (chk) chk.addEventListener('change', function() {
        chk.setAttribute('data-manual', '1');
        setCostoReceta(chk.checked);
    });

    initDataTable('tablaProductos', '{{ route("catalogo.productos.data") }}', [
        {data:'nombre',name:'nombre'},{data:'categoria.nombre',name:'categoria_id'},
        {data:'tipo_precio',name:'tipo_precio'},{data:'precio_usd',name:'precio_usd'},
        {data:'precio_bs',name:'precio_bs',orderable:false,searchable:false},{data:'activo',name:'activo',searchable:false},
        {data:'acciones',name:'acciones',orderable:false,searchable:false}
    ], { '#filtroCat': 1, '#filtroEstado': 5 });

    document.addEventListener('click', function(e) {
        var verBtn = e.target.closest('[data-act="ver-producto"]');
        if (!verBtn) return;
        e.preventDefault();
        fetch(verBtn.getAttribute('data-url'), { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.success || !res.data) { showToast('Error al cargar el producto', 'error'); return; }
            var p = res.data;
            var tipo = p.tipo_precio === 'margen' ? 'Por margen' : 'Definido';
            var costo = (p.tipo_precio === 'margen' && p.costo_usd) ? '$ '+Number(p.costo_usd).toFixed(2) : '—';
            var costoLabel = (p.tipo_precio === 'margen' && p.indexar_costo_receta) ? 'Costo (de receta)' : 'Costo (USD)';
            var receta = (p.receta && p.receta.nombre) ? p.receta.nombre : 'Sin receta vinculada';
            var activo = p.activo ? '<span class="badge-mov badge-entrada">Activo</span>' : '<span class="badge-mov badge-merma">Inactivo</span>';
            var html = '<div class="row g-3">';
            html += '<div class="col-md-12"><div class="text-muted" style="font-size:12px">Nombre</div><div class="fw-bold fs-5">'+(p.nombre || '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Categoría</div><div class="fw-bold">'+(p.categoria ? p.categoria.nombre : '—')+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Receta vinculada</div><div class="fw-bold">'+receta+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Tipo de precio</div><div class="fw-bold">'+tipo+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Estado</div><div>'+activo+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">'+costoLabel+'</div><div class="fw-bold font-monospace">'+costo+'</div></div>';
            html += '<div class="col-md-6"><div class="text-muted" style="font-size:12px">Precio USD</div><div class="fw-bold font-monospace">$ '+Number(p.precio_usd).toFixed(2)+'</div></div>';
            html += '<div class="col-md-12"><div class="text-muted" style="font-size:12px">Precio Bs</div><div class="fw-bold font-monospace">Bs '+Number(p.precio_bs || 0).toLocaleString('es-VE', {minimumFractionDigits:2})+'</div></div>';
            html += '</div>';
            document.getElementById('verProductoTitle').textContent = 'Detalle de producto';
            document.getElementById('verProductoBody').innerHTML = html;
            openModal('modalVerProducto');
        })
        .catch(function() { showToast('Error al cargar el producto', 'error'); });
    });
});
</script>
@endpush
