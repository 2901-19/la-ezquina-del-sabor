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
        <a href="{{ route('comandas.index') }}" class="nav-item" data-module="comandas">
            <i class="bi bi-receipt"></i>
            <span>Comandas</span>
        </a>
        <a href="{{ route('cocina.index') }}" class="nav-item" data-module="cocina">
            <i class="bi bi-fire"></i>
            <span>Cocina</span>
        </a>
        <div class="nav-section-label">Gestión</div>
        <a href="{{ route('catalogo.productos.index') }}" class="nav-item" data-module="catalogo">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>
        <a href="{{ route('catalogo.recetas.index') }}" class="nav-item" data-module="recetas">
            <i class="bi bi-journal-bookmark"></i>
            <span>Recetas</span>
        </a>
        <a href="{{ route('catalogo.combos.index') }}" class="nav-item" data-module="combos">
            <i class="bi bi-collection"></i>
            <span>Combos</span>
        </a>
        <a href="{{ route('inventario.materias-primas.index') }}" class="nav-item" data-module="inventario">
            <i class="bi bi-dropbox"></i>
            <span>Inventario</span>
        </a>
        <a href="{{ route('clientes.index') }}" class="nav-item" data-module="clientes">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>
        <a href="{{ route('creditos.index') }}" class="nav-item" data-module="creditos">
            <i class="bi bi-cash-stack"></i>
            <span>Créditos</span>
        </a>
        <div class="nav-section-label">Sistema</div>
        <a href="{{ route('reportes.index') }}" class="nav-item" data-module="reportes">
            <i class="bi bi-bar-chart"></i>
            <span>Reportes</span>
        </a>
        <a href="{{ route('sistema.usuarios.index') }}" class="nav-item" data-module="usuarios">
            <i class="bi bi-people-fill"></i>
            <span>Usuarios</span>
        </a>
        <a href="{{ route('jornada.cierre') }}" class="nav-item" data-module="cierre">
            <i class="bi bi-calendar-check"></i>
            <span>Cierre de Jornada</span>
        </a>
        <a href="{{ route('sistema.configuracion') }}" class="nav-item" data-module="configuracion">
            <i class="bi bi-gear"></i>
            <span>Configuración</span>
        </a>
    </nav>
    <div class="sidebar-foot">
        <div class="user-chip">
            <div class="user-avatar">AD</div>
            <div>
                <div class="user-name">Administrador</div>
                <div class="user-role">Admin</div>
            </div>
        </div>
    </div>
</aside>
