@php
    $rol = strtolower(trim(auth()->user()->rol->nombre ?? ''));
@endphp

<nav class="sidebar glass-card d-flex flex-column m-0 m-md-3 sidebar-sticky" id="sidebar">
    <a href="/" class="d-flex align-items-center mb-4 text-decoration-none text-body px-2">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 shadow-sm w-36-px h-36-px">
            <i class="bi bi-prism-fill"></i>
        </div>
        <span class="fs-4 fw-bold tracking-tight">PrismaNova</span>
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        
        @if(in_array($rol, ['admin', 'cajero', 'bodeguero']))
        <li class="nav-header small text-uppercase text-muted fw-bold mt-3 mb-2 px-2">Gestión</li>
        @endif

        @if(in_array($rol, ['admin', 'cajero']))
        <li>
            <a href="{{ route('ventas.pos') }}" class="nav-link {{ request()->routeIs('ventas.pos') ? 'active' : '' }}">
                <i class="bi bi-shop-window"></i> Punto de Venta
            </a>
        </li>
        <li>
            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                <i class="bi bi-cart-check-fill"></i> Ventas
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clientes
            </a>
        </li>
        <li>
            <a href="{{ route('caja.index') }}" class="nav-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Caja
            </a>
        </li>
        @endif

        @if(in_array($rol, ['admin', 'bodeguero']))
        <li>
            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Productos
            </a>
        </li>
        <li>
            <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Categorías
            </a>
        </li>
        <li>
            <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Proveedores
            </a>
        </li>
        <li>
            <a href="{{ route('compras.index') }}" class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <i class="bi bi-bag-plus-fill"></i> Compras
            </a>
        </li>
        @endif

        @if($rol === 'admin')
        <li class="nav-header small text-uppercase text-muted fw-bold mt-3 mb-2 px-2">Administración</li>
        <li>
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Usuarios
            </a>
        </li>
        <li>
            <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Reportes
            </a>
        </li>
        <li>
            <a href="{{ route('bitacora.index') }}" class="nav-link {{ request()->routeIs('bitacora.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Bitácora
            </a>
        </li>
        <li>
            <a href="{{ route('admin.mensajes.index') }}" class="nav-link {{ request()->routeIs('admin.mensajes.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-open-fill"></i> Mensajes
            </a>
        </li>
        <li>
            <a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Configuración
            </a>
        </li>
        <li>
            <a href="{{ route('admin.health') }}" class="nav-link {{ request()->routeIs('admin.health') ? 'active' : '' }}">
                <i class="bi bi-heart-pulse-fill"></i> Estado Sistema
            </a>
        </li>
        @endif

        @if($rol === 'cliente')
        <li class="nav-header small text-uppercase text-muted fw-bold mt-3 mb-2 px-2">Mi Cuenta</li>
        <li>
            <a href="{{ route('tienda.catalogo') }}" class="nav-link {{ request()->routeIs('tienda.catalogo') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Catálogo
            </a>
        </li>
        <li>
            <a href="{{ route('mis-compras.index') }}" class="nav-link {{ request()->routeIs('mis-compras.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check-fill"></i> Mis Compras
            </a>
        </li>
        <li>
            <a href="{{ route('tienda.carrito') }}" class="nav-link {{ request()->routeIs('tienda.carrito') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i> Mi Carrito
            </a>
        </li>
        @endif

        <li class="nav-item mt-3">
            <a href="{{ route('contact.index') }}" class="nav-link {{ request()->routeIs('contact.index') ? 'active' : '' }}">
                <i class="bi bi-envelope-fill"></i> Contáctanos
            </a>
        </li>

        <li class="mt-auto">
            <hr class="text-muted">
            <a href="{{ route('perfil') }}" class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 w-32-px h-32-px">
                        {{ substr(auth()->user()->nombre ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold small">{{ auth()->user()->nombre ?? 'Usuario' }} {{ auth()->user()->apellido ?? '' }}</div>
                        <div class="text-muted x-small">{{ ucfirst($rol) }}</div>
                    </div>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="nav-link text-danger w-100 text-start">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </button>
            </form>
        </li>
    </ul>
</nav>
