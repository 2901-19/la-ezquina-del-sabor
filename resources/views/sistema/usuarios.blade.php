@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Sistema</div>
        <h1 class="page-title">Usuarios y Permisos</h1>
        <p class="page-sub">Gestiona los usuarios del sistema</p>
    </div>
    <div class="head-actions">
        <button class="btn-primary-brand" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bi bi-person-plus"></i> Nuevo usuario
        </button>
    </div>
</div>

<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar usuario…" class="input-brand" aria-label="Buscar usuario" />
    </div>
    <div class="filter-selects">
    <select id="fRol" class="select-brand" aria-label="Filtrar por rol">
        <option value="">Todos</option>
        <option value="Administrador">Administrador</option>
        <option value="Recepcionista">Recepcionista</option>
        <option value="Cocinero">Cocinero</option>
    </select>
    <select id="fEstado" class="select-brand" aria-label="Filtrar por estado">
        <option value="">Todos</option>
        <option value="1">Activos</option>
        <option value="0">Inactivos</option>
    </select>
    </div>
</div>

<div class="table-panel">
    <table class="table display" id="tablaUsuarios" style="width:100%">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Último acceso</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUsuario" class="ajax-form" method="POST" action="{{ route('sistema.usuarios.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header modal-header-brand">
                    <h5 class="modal-title" id="modalUsuarioTitle">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom:16px;">
                        <label class="form-label-brand">Nombre completo <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control input-brand" required pattern="[A-Za-záéíóúñÁÉÍÓÚÑ\s\-\.]+" minlength="2" maxlength="255">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="form-label-brand">Usuario <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="username" class="form-control input-brand" required pattern="[a-zA-Z0-9_\-]+" minlength="3" maxlength="100">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="form-label-brand">Contraseña <span style="color:var(--danger)">*</span></label>
                        <input type="password" name="password" class="form-control input-brand" required minlength="6">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="form-label-brand">Rol <span style="color:var(--danger)">*</span></label>
                        <select name="rol_id" class="form-select select-brand" required>
                            <option value="">Selecciona rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Recepcionista</option>
                            <option value="3">Cocinero</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-brand">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaUsuarios', '{{ route("sistema.usuarios.data") }}', [
        {data:'nombre_completo',name:'nombre_completo'},
        {data:'rol.nombre',name:'rol_id'},{data:'activo',name:'activo'},
        {data:'created_at',name:'created_at'},{data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
