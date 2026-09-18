import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';
window.jQuery = jQuery;
window.$ = jQuery;
window.Swal = Swal;

var TASA_BCV = 818.00;
window.TASA_BCV = TASA_BCV;

document.addEventListener('DOMContentLoaded', function() {
    var tasaEl = document.getElementById('tasaBcv');
    if (tasaEl) {
        var tasa = parseFloat(tasaEl.textContent);
        if (!isNaN(tasa) && tasa > 0) {
            TASA_BCV = tasa;
            window.TASA_BCV = tasa;
        }
    }
});

function showToast(msg, tipo) {
    var map = {success:'success', error:'error', info:'info'};
    Swal.fire({toast:true, position:'top-end', icon:map[tipo], title:msg, showConfirmButton:false, timer:3000});
}
window.showToast = showToast;

function openModal(modalId) { bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId)).show(); }
window.openModal = openModal;

function closeModal(modalId) { var m = bootstrap.Modal.getInstance(document.getElementById(modalId)); if(m) m.hide(); }
window.closeModal = closeModal;

function confirmarBorrar(url) {
    Swal.fire({title:'¿Estás seguro?',text:'No podrás recuperar este registro',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'}).then(function(r){
        if(r.isConfirmed){
            var token = document.querySelector('meta[name="csrf-token"]');
            var csrf = token ? token.getAttribute('content') : '';
            fetch(url,{method:'DELETE',credentials:'same-origin',headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'}})
            .then(function(res){return res.json();})
            .then(function(data){if(data.success){showToast(data.message,'success');location.reload();}else{showToast('Error al eliminar','error');}})
            .catch(function(){showToast('Error','error');});
        }
    });
}
window.confirmarBorrar = confirmarBorrar;

function cargarModalEdicion(url, modalId) {
    fetch(url).then(function(r){return r.json();}).then(function(data){
        if(data.success){var formData=data.data;Object.keys(formData).forEach(function(key){var el=document.querySelector('[name="'+key+'"]');if(el){if(el.type==='checkbox')el.checked=formData[key];else el.value=formData[key];}});openModal(modalId);}
    }).catch(function(){showToast('Error al cargar datos','error');});
}
window.cargarModalEdicion = cargarModalEdicion;

function setTipoPrecio(tipo) {
    var fTipo = document.getElementById('fTipoPrecio');
    var btnMargen = document.getElementById('tipoMargen');
    var btnDefinido = document.getElementById('tipoDefinido');
    if (tipo === 'margen') {
        document.getElementById('bloqueMargen').style.display = 'block';
        document.getElementById('bloqueDefinido').style.display = 'none';
        if (fTipo) fTipo.value = 'margen';
    } else {
        document.getElementById('bloqueMargen').style.display = 'none';
        document.getElementById('bloqueDefinido').style.display = 'block';
        if (fTipo) fTipo.value = 'definido';
    }
    if (btnMargen) btnMargen.classList.toggle('active', tipo === 'margen');
    if (btnDefinido) btnDefinido.classList.toggle('active', tipo === 'definido');
    updatePrecioUsd();
}
window.setTipoPrecio = setTipoPrecio;

function updatePrecioUsd() {
    var fTipo = document.getElementById('fTipoPrecio');
    var fPrecioUsd = document.getElementById('fPrecioUsd');
    if (!fTipo || !fPrecioUsd) return;
    if (fTipo.value === 'definido') {
        var precioDef = document.getElementById('fPrecioDef');
        fPrecioUsd.value = precioDef ? (parseFloat(precioDef.value) || 0) : 0;
    } else {
        var costo = document.getElementById('fCosto');
        var margen = document.getElementById('fMargen');
        var c = costo ? (parseFloat(costo.value) || 0) : 0;
        var m = margen ? (parseFloat(margen.value) || 0) : 0;
        fPrecioUsd.value = (c + c * (m / 100)).toFixed(2);
    }
}

function initPriceCalc() {
    var fCosto = document.getElementById('fCosto');
    var fMargen = document.getElementById('fMargen');
    var fPrecioDef = document.getElementById('fPrecioDef');
    if (fCosto && fMargen) {
        function calc() {
            var costo = parseFloat(fCosto.value) || 0;
            var margen = parseFloat(fMargen.value) || 0;
            var ganancia = costo * (margen / 100);
            var precio = costo + ganancia;
            var precioBs = precio * TASA_BCV;
            document.getElementById('cCosto').textContent = '$ ' + costo.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('cGanancia').textContent = '$ ' + ganancia.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('cPrecio').textContent = '$ ' + precio.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('cPrecioBs').textContent = 'Bs ' + precioBs.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('resUsd').textContent = '$ ' + precio.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('resBs').textContent = 'Bs ' + precioBs.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            updatePrecioUsd();
        }
        fCosto.addEventListener('input', calc);
        fMargen.addEventListener('input', calc);
        calc();
    }
    if (fPrecioDef) {
        fPrecioDef.addEventListener('input', function() {
            var v = parseFloat(fPrecioDef.value) || 0;
            var bs = v * TASA_BCV;
            document.getElementById('resUsd').textContent = '$ ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('resBs').textContent = 'Bs ' + bs.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            updatePrecioUsd();
        });
    }
}

function initDate() {
    var el = document.getElementById('todayDate');
    if (el) el.textContent = new Date().toLocaleDateString('es-VE',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
}

function formatDate(iso) {
    if (!iso) return '';
    var d = new Date(iso);
    var Y = d.getFullYear();
    var M = String(d.getMonth() + 1).padStart(2, '0');
    var D = String(d.getDate()).padStart(2, '0');
    var h = d.getHours();
    var ampm = h >= 12 ? 'pm' : 'am';
    var h12 = h % 12 || 12;
    var m = String(d.getMinutes()).padStart(2, '0');
    return Y + '-' + M + '-' + D + ' | ' + h12 + ':' + m + ampm;
}
window.formatDate = formatDate;

function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.querySelector('.sidebar-overlay');
    if (!sidebar) return;
    var isMobile = window.innerWidth < 992;
    if (isMobile) {
        sidebar.classList.toggle('sidebar--open');
    } else {
        sidebar.classList.toggle('sidebar--oculto');
    }
    if (overlay) overlay.classList.toggle('active');
}
window.toggleSidebar = toggleSidebar;

function initDataTable(tableId, url, columns, filters) {
    if (typeof $.fn.DataTable !== 'undefined' && $('#' + tableId).length > 0) {
        var table = $('#' + tableId).DataTable({
            processing: true, serverSide: true, ajax: url,
            columns: columns, dom: '<"row mb-2"<"col-sm-6"l>>rt<"row dt-footer"<"col-sm-6"i><"col-sm-6 text-end"p>>', language: {
                search: '', lengthMenu: 'Mostrar _MENU_ entradas',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                infoEmpty: 'Sin resultados', infoFiltered: '(filtrado de _MAX_)',
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                },
                zeroRecords: 'Sin resultados', loadingRecords: 'Cargando...'
            },
            responsive: true, pageLength: 10
        });
        if (filters && typeof filters === 'object') {
            Object.keys(filters).forEach(function(selector) {
                var colIdx = filters[selector];
                $(selector).on('change', function() {
                    var val = $(this).val() || '';
                    table.column(colIdx).search(val).draw();
                });
            });
        }
        var buscarInput = document.getElementById('buscar');
        if (buscarInput) {
            buscarInput.addEventListener('input', function() {
                table.search(this.value).draw();
            });
        }
    }
}
window.initDataTable = initDataTable;

document.addEventListener('DOMContentLoaded', function() {
    initDate();
    initPriceCalc();

    document.querySelectorAll('[data-bs-target]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var modalId = this.getAttribute('data-bs-target');
            var modal = document.querySelector(modalId);
            if (!modal) return;
            var form = modal.querySelector('.ajax-form');
            if (!form) return;
            form.reset();
            var methodField = form.querySelector('[name="_method"]');
            if (methodField) methodField.value = 'POST';
            var storeAction = form.getAttribute('action').replace(/\/\d+$/, '');
            form.setAttribute('action', storeAction);
            var title = form.querySelector('.modal-title');
            if (title && title.textContent.indexOf('Nuevo') === -1 && title.textContent.indexOf('Editar') !== -1) {
                title.textContent = title.textContent.replace('Editar', 'Nuevo');
            }
        });
    });

    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form.classList.contains('ajax-form')) return;
        if (!form.reportValidity()) return;
        e.preventDefault();

        var url = form.getAttribute('action');
        var method = form.getAttribute('method') || 'POST';
        var token = document.querySelector('meta[name="csrf-token"]');
        var csrf = token ? token.getAttribute('content') : '';

        var formData = new FormData(form);
        var button = form.querySelector('[type="submit"]');
        var originalText = button ? button.innerHTML : '';
        if (button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        }

        var fetchOptions = {
            method: method,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        };

        fetch(url, fetchOptions)
        .then(function(res) {
            var contentType = res.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') === -1) {
                return { status: res.status, body: { success: false, message: 'Respuesta inesperada del servidor (' + res.status + ')' } };
            }
            return res.json().then(function(body) {
                return { status: res.status, body: body };
            });
        })
        .then(function(result) {
            var body = result.body;

            if (result.status === 422) {
                var msgs = [];
                if (body.errors) {
                    Object.keys(body.errors).forEach(function(key) {
                        var errArr = body.errors[key];
                        if (Array.isArray(errArr)) {
                            errArr.forEach(function(m) { msgs.push(m); });
                        } else if (typeof errArr === 'string') {
                            msgs.push(errArr);
                        }
                    });
                }
                Swal.fire({ icon: 'error', title: 'Errores de validación', text: msgs.join('\n') || 'Verifica los campos.' });
                return;
            }

            if (result.status === 200 || result.status === 201) {
                var modal = form.closest('.modal');
                if (modal) closeModal(modal.id);

                Swal.fire({ icon: 'success', title: '¡Éxito!', text: body.message || 'Operación exitosa', timer: 2000, showConfirmButton: false }).then(function() {
                    location.reload();
                });
                return;
            }

            Swal.fire({ icon: 'error', title: 'Error', text: body.message || 'Ocurrió un error inesperado.' });
        })
        .catch(function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor.' });
        })
        .finally(function() {
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-act="borrar"]');
        if (btn) {
            e.preventDefault();
            var deleteUrl = btn.getAttribute('data-url');
            if (deleteUrl) {
                confirmarBorrar(deleteUrl);
            }
            return;
        }

        var editBtn = e.target.closest('[data-act="editar"]');
        if (editBtn) {
            e.preventDefault();
            var editId = editBtn.getAttribute('data-id');
            var tablePanel = editBtn.closest('.table-panel');
            var editForm = null;
            if (tablePanel && tablePanel.nextElementSibling && tablePanel.nextElementSibling.querySelector('.ajax-form')) {
                editForm = tablePanel.nextElementSibling.querySelector('.ajax-form');
            }
            if (!editForm) {
                var allForms = document.querySelectorAll('.ajax-form');
                for (var i = 0; i < allForms.length; i++) {
                    if (allForms[i].querySelector('[name="nombre"]')) {
                        editForm = allForms[i];
                        break;
                    }
                }
            }
            if (!editForm) return;

            var showUrl = editForm.getAttribute('action').replace(/\/store$/, '') + '/' + editId;
            var updateUrl = editForm.getAttribute('action').replace(/\/store$/, '') + '/' + editId;

            fetch(showUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success || !data.data) { showToast('Error al cargar datos', 'error'); return; }
                var record = data.data;
                Object.keys(record).forEach(function(key) {
                    var el = editForm.querySelector('[name="' + key + '"]');
                    if (!el) return;
                    if (el.type === 'checkbox') {
                        el.checked = !!record[key];
                    } else if (el.tagName === 'SELECT') {
                        el.value = record[key] !== null ? record[key] : '';
                    } else {
                        el.value = record[key] !== null ? record[key] : '';
                    }
                });

                var methodField = editForm.querySelector('[name="_method"]');
                if (methodField) methodField.value = 'PUT';
                editForm.setAttribute('action', updateUrl);

                var title = editForm.querySelector('.modal-title');
                if (title) title.textContent = 'Editar';

                if (typeof window.cargarDetalleReceta === 'function') {
                    window.cargarDetalleReceta(record);
                }

                if (typeof window.cargarDetalleProducto === 'function') {
                    window.cargarDetalleProducto(record);
                }

                var modal = editForm.closest('.modal');
                if (modal) openModal(modal.id);
            })
            .catch(function() { showToast('Error al cargar datos', 'error'); });
            return;
        }

        var verBtn = e.target.closest('[data-act="ver"]');
        if (verBtn) {
            e.preventDefault();
            var verId = verBtn.getAttribute('data-id');
            var verUrl = '/comandas/' + verId + '/show';
            fetch(verUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(c) {
                renderComanda(c);
                openModal('modalVerComanda');
            })
            .catch(function(e) { console.error('Error show comanda:', e); showToast('Error al cargar comanda', 'error'); });
            return;
        }
    });
});

function renderComanda(c) {
    var num = String(c.numero_correlativo_diario).padStart(3, '0');
    var estadoMap = { montar: { label: 'Montar', cls: 'bg-info' }, entrega: { label: 'Entrega', cls: 'bg-primary' }, cobrar: { label: 'Cobrar', cls: 'bg-warning text-dark' }, cerrada: { label: 'Cerrada', cls: 'bg-secondary' } };
    var est = estadoMap[c.estado_comanda] || { label: c.estado_comanda, cls: 'bg-secondary' };
    var cliente = (c.cliente && c.cliente.nombre) ? c.cliente.nombre : (c.nombre_cliente_temporal || 'Sin cliente');
    var telefono = (c.cliente && c.cliente.telefono) ? c.cliente.telefono : (c.telefono_delivery || '—');
    var usuario = c.usuario ? c.usuario.nombre_completo : '—';
    var rol = (c.usuario && c.usuario.rol) ? c.usuario.rol.nombre : '';
    var notas = c.notas_generales || '';

    var items = '';
    if (c.comandaDetalles && c.comandaDetalles.length) {
        c.comandaDetalles.forEach(function(d, i) {
            var sub = (d.cantidad * d.precio_unitario_usd).toFixed(2);
            var tipoMap = { comer_aqui: 'Aquí', llevar: 'Llevar', delivery: 'Delivery' };
            var tipo = tipoMap[d.tipo_entrega] || d.tipo_entrega;
            var note = d.nota_producto ? '<br><small class="text-muted fst-italic">' + d.nota_producto + '</small>' : '';
            items += '<tr><td>' + (i + 1) + '</td><td>' + (d.producto ? d.producto.nombre : '—') + note + '</td><td class="text-center">' + d.cantidad + '</td><td class="text-end font-monospace">$' + Number(d.precio_unitario_usd).toFixed(2) + '</td><td class="text-end font-monospace">$' + sub + '</td><td><span class="badge bg-light text-dark">' + tipo + '</span></td></tr>';
        });
    } else {
        items = '<tr><td colspan="6" class="text-center text-muted py-3">Sin productos</td></tr>';
    }

    var html = '';
    html += '<div class="mb-3 pb-3 border-bottom">';
    html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-info-circle"></i> Información General</h6>';
    html += '<div class="row g-3">';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Número</div><div class="fw-bold" style="font-size:18px">#' + num + '</div></div>';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Estado</div><div><span class="badge ' + est.cls + '">' + est.label + '</span></div></div>';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Fecha</div><div class="fw-bold font-monospace">' + formatDate(c.fecha_creacion) + '</div></div>';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Tasa BCV</div><div class="fw-bold font-monospace">' + Number(c.tasa_bcv_aplicada).toFixed(2) + '</div></div>';
    html += '</div></div>';

    html += '<div class="mb-3 pb-3 border-bottom">';
    html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-person"></i> Cliente</h6>';
    html += '<div class="row g-3">';
    html += '<div class="col-md-4"><div class="text-muted" style="font-size:12px">Nombre</div><div class="fw-bold">' + cliente + '</div></div>';
    html += '<div class="col-md-4"><div class="text-muted" style="font-size:12px">Teléfono</div><div class="font-monospace">' + telefono + '</div></div>';
    html += '</div></div>';

    html += '<div class="mb-3 pb-3 border-bottom">';
    html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-person-badge"></i> Atendido por</h6>';
    html += '<div class="row g-3">';
    html += '<div class="col-md-4"><div class="text-muted" style="font-size:12px">Usuario</div><div class="fw-bold">' + usuario + (rol ? ' <span class="text-muted">(' + rol + ')</span>' : '') + '</div></div>';
    html += '</div></div>';

    html += '<div class="mb-3 pb-3 border-bottom">';
    html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-bag"></i> Productos</h6>';
    html += '<div class="table-responsive"><table class="table table-sm align-middle mb-0">';
    html += '<thead><tr class="table-light"><th>#</th><th>Producto</th><th class="text-center">Cant</th><th class="text-end">P. Unit</th><th class="text-end">Subtotal</th><th>Tipo</th></tr></thead>';
    html += '<tbody>' + items + '</tbody></table></div></div>';

    if (notas) {
        html += '<div class="mb-3 pb-3 border-bottom">';
        html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-chat-left-text"></i> Notas</h6>';
        html += '<p class="mb-0" style="font-size:14px">' + notas + '</p></div>';
    }

    html += '<div class="mt-2">';
    html += '<h6 class="text-uppercase text-muted fw-semibold mb-2" style="font-size:11px;letter-spacing:.08em"><i class="bi bi-calculator"></i> Totales</h6>';
    html += '<div class="row g-3">';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Total USD</div><div class="fw-bold" style="font-size:20px;color:var(--accent)">$' + Number(c.total_usd).toFixed(2) + '</div></div>';
    html += '<div class="col-md-3"><div class="text-muted" style="font-size:12px">Total Bs</div><div class="fw-bold font-monospace" style="font-size:20px">Bs ' + Number(c.total_ve).toLocaleString('es-VE', { minimumFractionDigits: 2 }) + '</div></div>';
    html += '</div></div>';

    document.getElementById('verComandaTitle').textContent = 'Comanda #' + num + ' · ' + est.label;
    document.getElementById('verComandaBody').innerHTML = html;
}
