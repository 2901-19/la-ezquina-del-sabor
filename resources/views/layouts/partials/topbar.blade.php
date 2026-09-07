<header class="topbar">
    <div class="topbar-left">
        <button class="btn-jornada" id="hamburger" type="button" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-date" id="todayDate"></div>
    </div>
    <div class="topbar-right">
        <div class="tasa-chip">
            <span class="tasa-label">BCV</span>
            <span id="tasaBcv">{{ $tasaBcv ?? '42.50' }}</span>
        </div>
        <div class="work-status open">
            <span class="dot"></span>
            Jornada Abierta
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="icon-btn" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>
