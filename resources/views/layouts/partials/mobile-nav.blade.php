<nav class="offcanvas-nav">
    <a href="{{ route('dashboard') }}" class="nav-item" data-module="dashboard">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('comandas.index') }}" class="nav-item" data-module="comandas">
        <i class="bi bi-receipt"></i>
        <span>Comandas</span>
    </a>
    <a href="{{ route('catalogo.productos.index') }}" class="nav-item" data-module="catalogo">
        <i class="bi bi-box-seam"></i>
        <span>Productos</span>
    </a>
    <a href="{{ route('inventario.materias-primas.index') }}" class="nav-item" data-module="inventario">
        <i class="bi bi-dropbox"></i>
        <span>Inventario</span>
    </a>
    <a href="{{ route('clientes.index') }}" class="nav-item" data-module="clientes">
        <i class="bi bi-people"></i>
        <span>Clientes</span>
    </a>
    <a href="{{ route('reportes.index') }}" class="nav-item" data-module="reportes">
        <i class="bi bi-bar-chart"></i>
        <span>Reportes</span>
    </a>
</nav>
