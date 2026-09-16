<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <i class="bi bi-shop"></i>
        </div>
        <div class="sidebar-name">La Esquina<br>del Sabor</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Operación</div>
        <a href="{{ route('dashboard') }}" class="nav-item" data-module="dashboard">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        @can('permiso', 'crear_comanda')
        <a href="{{ route('comandas.index') }}" class="nav-item" data-module="comandas">
            <i class="bi bi-receipt"></i>
            <span>Comandas</span>
        </a>
        @endcan
        @can('permiso', 'marcar_entrega')
        <a href="{{ route('cocina.index') }}" class="nav-item" data-module="cocina">
            <i class="bi bi-fire"></i>
            <span>Cocina</span>
        </a>
        @endcan
        <div class="nav-section-label">Gestión</div>
        @can('permiso', 'ver_catalogo')
        <a href="{{ route('catalogo.productos.index') }}" class="nav-item" data-module="catalogo">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>
        @endcan
        @can('permiso', 'ver_catalogo')
        <a href="{{ route('catalogo.recetas.index') }}" class="nav-item" data-module="recetas">
            <i class="bi bi-journal-bookmark"></i>
            <span>Recetas</span>
        </a>
        @endcan
        @can('permiso', 'ver_catalogo')
        <a href="{{ route('catalogo.combos.index') }}" class="nav-item" data-module="combos">
            <i class="bi bi-collection"></i>
            <span>Combos</span>
        </a>
        @endcan
        @can('permiso', 'ver_inventario')
        <a href="{{ route('inventario.materias-primas.index') }}" class="nav-item" data-module="inventario">
            <i class="bi bi-dropbox"></i>
            <span>Inventario</span>
        </a>
        @endcan
        @can('permiso', 'ver_clientes')
        <a href="{{ route('clientes.index') }}" class="nav-item" data-module="clientes">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>
        @endcan
        @can('permiso', 'gestionar_creditos')
        <a href="{{ route('creditos.index') }}" class="nav-item" data-module="creditos">
            <i class="bi bi-cash-stack"></i>
            <span>Créditos</span>
        </a>
        @endcan
        <div class="nav-section-label">Sistema</div>
        @can('permiso', 'ver_reportes')
        <a href="{{ route('reportes.index') }}" class="nav-item" data-module="reportes">
            <i class="bi bi-bar-chart"></i>
            <span>Reportes</span>
        </a>
        @endcan
        @can('permiso', 'ver_usuarios')
        <a href="{{ route('sistema.usuarios.index') }}" class="nav-item" data-module="usuarios">
            <i class="bi bi-people-fill"></i>
            <span>Usuarios</span>
        </a>
        @endcan
        @can('permiso', 'cierre_jornada')
        <a href="{{ route('jornada.cierre') }}" class="nav-item" data-module="cierre">
            <i class="bi bi-calendar-check"></i>
            <span>Cierre de Jornada</span>
        </a>
        @endcan
        @can('permiso', 'configurar')
        <a href="{{ route('sistema.configuracion') }}" class="nav-item" data-module="configuracion">
            <i class="bi bi-gear"></i>
            <span>Configuración</span>
        </a>
        @endcan
    </nav>
    <div class="sidebar-foot">
        <div class="user-chip">
            <div class="user-avatar">{{ substr(auth()->user()->nombre_completo, 0, 2) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->nombre_completo }}</div>
                <div class="user-role">{{ auth()->user()->rol->nombre }}</div>
            </div>
        </div>
    </div>
</aside>
