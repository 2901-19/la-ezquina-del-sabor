import 'bootstrap';
import jQuery from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';
window.jQuery = jQuery;
window.$ = jQuery;
window.Swal = Swal;

var TASA_BCV = 42.50;
window.TASA_BCV = TASA_BCV;

function showToast(msg, tipo) {
    var map = {success:'success', error:'error', info:'info'};
    Swal.fire({toast:true, position:'top-end', icon:map[tipo], title:msg, showConfirmButton:false, timer:3000});
}
window.showToast = showToast;

function openModal(modalId) { new bootstrap.Modal(document.getElementById(modalId)).show(); }
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
            var id = btn.getAttribute('data-id');
            var token = document.querySelector('meta[name="csrf-token"]');
            var csrf = token ? token.getAttribute('content') : '';
            var baseUrl = btn.closest('.ajax-form') ? btn.closest('.ajax-form').getAttribute('action') : '';
            if (baseUrl && id) {
                confirmarBorrar(baseUrl + '/' + id);
            }
            return;
        }

        var editBtn = e.target.closest('[data-act="editar"]');
        if (editBtn) {
            e.preventDefault();
            var editId = editBtn.getAttribute('data-id');
            var editForm = editBtn.closest('.table-panel') ? editBtn.closest('.table-panel').nextElementSibling : null;
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

                var modal = editForm.closest('.modal');
                if (modal) openModal(modal.id);
            })
            .catch(function() { showToast('Error al cargar datos', 'error'); });
            return;
        }
    });
});
