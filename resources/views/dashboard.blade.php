@extends('layouts.app')
@section('title','Dashboard')
@section('content')
{{-- Obtener el rol del usuario actual para renderizar vistas condicionales --}}
@php($rol = strtolower(trim(auth()->user()->rol->nombre ?? '')))

{{-- Encabezado de bienvenida con nuevo gradiente --}}
<div class="glass-card p-4 p-md-5 mb-4 text-white position-relative overflow-hidden bg-primary-gradient-borderless">
    <div class="position-relative z-1">
        <h2 class="fw-bold mb-2 fs-3 fs-md-2">¡Hola, {{ auth()->user()->nombre }}! 👋</h2>
        <p class="mb-0 opacity-100 fs-6 fs-md-5">Bienvenido a tu panel de control de PrismaNova.</p>
    </div>
    <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-md-block dashboard-header-decoration"></div>
</div>

@isset($stats)
{{-- Fila de KPIs (Indicadores Clave de Desempeño) --}}
<div class="row g-3 g-md-4 mb-4">
    {{-- KPIs exclusivos para ADMIN y BODEGUERO --}}
    @if(in_array($rol, ['admin', 'bodeguero']))
        @if($rol === 'admin')
        <div class="col-sm-6 col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Ventas Hoy</div>
                            <div class="h4 mb-0 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($stats['ventas_hoy_total'] ?? 0,2) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <span class="text-success fw-bold"><i class="bi bi-graph-up-arrow"></i> {{ $stats['ventas_hoy_count'] ?? 0 }}</span> transacciones
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="bi bi-bag-check fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Compras Hoy</div>
                            <div class="h4 mb-0 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($stats['compras_hoy_total'] ?? 0,2) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <span class="text-danger fw-bold"><i class="bi bi-arrow-down"></i> {{ $stats['compras_hoy_count'] ?? 0 }}</span> operaciones
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Ventas Mes</div>
                            <div class="h4 mb-0 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($stats['ventas_mes_total'] ?? 0,2) }}</div>
                        </div>
                    </div>
                    <div class="progress rounded-pill h-4px">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- KPI Stock Bajo (Visible para Admin y Bodeguero) --}}
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('productos.index', ['filtro' => 'stock_bajo']) }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Stock Bajo</div>
                            <div class="h4 mb-0 fw-bold text-body">{{ $stats['stock_bajo'] ?? 0 }}</div>
                        </div>
                    </div>
                     <div class="small text-muted">Productos requieren atención</div>
                </div>
            </a>
        </div>
        
        @if($rol === 'bodeguero')
        <div class="col-sm-6 col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                            <i class="bi bi-boxes fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Total Productos</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['productos_total'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">En inventario</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="bi bi-cart-check fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Compras Mes</div>
                            <div class="h4 mb-0 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($stats['compras_mes_total'] ?? 0,2) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">Gasto en abastecimiento</div>
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- KPIs exclusivos para CLIENTE --}}
    @if($rol === 'cliente')
        <div class="col-sm-6 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="bi bi-bag-check fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Mis Compras</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['mis_compras'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">Pedidos totales realizados</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Gasto Mes</div>
                            <div class="h4 mb-0 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($stats['gasto_mes'] ?? 0,2) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">Acumulado este mes</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Alerta de Stock Bajo (Tabla Rápida) --}}
    @if(isset($stats['productos_stock_bajo']) && count($stats['productos_stock_bajo']) > 0)
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                <h5 class="mb-0 fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Productos con Stock Crítico</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-body">
                    <thead class="bg-warning bg-opacity-10 text-warning small text-uppercase">
                        <tr>
                            <th class="ps-4 border-bottom border-light border-opacity-10">Producto</th>
                            <th class="text-center border-bottom border-light border-opacity-10">Stock Actual</th>
                            <th class="text-center border-bottom border-light border-opacity-10">Mínimo</th>
                            <th class="text-end pe-4 border-bottom border-light border-opacity-10">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['productos_stock_bajo'] as $prod)
                        <tr class="hover-bg-white-5">
                            <td class="ps-4 fw-medium border-bottom border-light border-opacity-10">{{ $prod->nombre }}</td>
                            <td class="text-center fw-bold text-danger border-bottom border-light border-opacity-10">{{ $prod->stock }}</td>
                            <td class="text-center text-muted border-bottom border-light border-opacity-10">{{ $prod->stock_minimo }}</td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <a href="{{ route('productos.edit', $prod->id_producto) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                    <i class="bi bi-pencil me-1"></i>Gestionar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- KPIs comunes para ADMIN y CAJERO --}}
    @if(in_array($rol,['admin','cajero']))
        <div class="col-md-3">
            <div class="glass-card h-100">
                <div class="card-body">
                     <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small fw-medium text-uppercase">Estado Caja</div>
                            <div class="h5 mb-0 fw-bold">{{ ($stats['caja_abierta'] ?? false) ? 'Abierta' : 'Cerrada' }}</div>
                        </div>
                    </div>
                    @if($rol === 'cajero')
                    <div class="small text-muted">
                         <span class="text-success fw-bold"><i class="bi bi-graph-up-arrow"></i> {{ $stats['ventas_hoy_usuario_count'] ?? 0 }}</span> ventas tuyas hoy
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endisset

{{-- Accesos rápidos a módulos principales --}}
<h5 class="fw-bold mb-3 text-secondary">Accesos Rápidos</h5>
<div class="row g-4 mb-5">
    @if(in_array($rol, ['admin', 'cajero', 'bodeguero']))
        {{-- Clientes: Admin y Cajero --}}
        @if(in_array($rol, ['admin', 'cajero']))
        <div class="col-md-3">
            <a href="{{ route('clientes.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3 text-primary">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Clientes</h6>
                    <p class="small text-muted mb-0">Gestión de cartera</p>
                </div>
            </a>
        </div>
        @endif

        {{-- Productos: Admin y Bodeguero --}}
        @if(in_array($rol, ['admin', 'bodeguero']))
        <div class="col-md-3">
            <a href="{{ route('productos.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-inline-block mb-3 text-warning">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Productos</h6>
                    <p class="small text-muted mb-0">Catálogo e inventario</p>
                </div>
            </a>
        </div>
        @endif

        {{-- Punto de Venta: Admin y Cajero --}}
        @if(in_array($rol, ['admin', 'cajero']))
        <div class="col-md-3">
            <a href="{{ route('ventas.pos') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3 text-success">
                        <i class="bi bi-shop-window fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Punto de Venta</h6>
                    <p class="small text-muted mb-0">Facturación rápida</p>
                </div>
            </a>
        </div>
        
        <div class="col-md-3">
            <a href="{{ route('ventas.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3 text-success">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Ventas</h6>
                    <p class="small text-muted mb-0">Historial de ventas</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('caja.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-block mb-3 text-info">
                        <i class="bi bi-cash-coin fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Caja</h6>
                    <p class="small text-muted mb-0">Apertura y Cierre</p>
                </div>
            </a>
        </div>
        @endif

        {{-- Compras: Admin y Bodeguero --}}
        @if(in_array($rol, ['admin', 'bodeguero']))
        <div class="col-md-3">
            <a href="{{ route('compras.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 d-inline-block mb-3 text-danger">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Compras</h6>
                    <p class="small text-muted mb-0">Abastecimiento</p>
                </div>
            </a>
        </div>
        @endif
    @elseif($rol === 'cliente')
        <div class="col-md-3">
            <a href="{{ route('tienda.catalogo') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3 text-primary">
                        <i class="bi bi-shop fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Catálogo</h6>
                    <p class="small text-muted mb-0">Ver productos</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('tienda.carrito') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-inline-block mb-3 text-warning">
                        <i class="bi bi-cart fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Mi Carrito</h6>
                    <p class="small text-muted mb-0">Revisar pedido</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mis-compras.index') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3 text-success">
                        <i class="bi bi-bag-check fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Mis Compras</h6>
                    <p class="small text-muted mb-0">Historial de pedidos</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('perfil') }}" class="glass-card h-100 text-decoration-none transform-hover d-block">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-block mb-3 text-info">
                        <i class="bi bi-person-circle fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Mi Perfil</h6>
                    <p class="small text-muted mb-0">Datos personales</p>
                </div>
            </a>
        </div>
    @endif
</div>

<div class="row g-4">
    {{-- Sección Gráficas --}}
    @if($rol==='admin')
    <div class="col-lg-8">
        <div class="glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">Ventas del Mes</h5>
                <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3 shadow-sm transform-hover">Ver Reportes</a>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="height: 300px;">
                    <canvas id="chartDias"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card h-100">
            <div class="card-header bg-transparent pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-primary">Top Productos</h5>
            </div>
            <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                <div class="chart-container-md">
                    <canvas id="chartTopProductos"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($rol==='cajero')
    <div class="col-lg-8">
        <div class="glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">Mis Ventas (Hoy)</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="h-300px">
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card h-100">
            <div class="card-header bg-transparent pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-primary">Top Productos Hoy</h5>
            </div>
            <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                <div class="chart-container-md">
                    <canvas id="chartTopProductosCajero"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sección Alertas Stock --}}
    @if(in_array($rol,['admin','bodeguero']) && !empty($stats['productos_stock_bajo']) && $stats['productos_stock_bajo']->count() > 0)
    <div class="col-12">
        <div class="glass-card border-start border-danger border-4">
            <div class="card-header pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Alertas de Stock</h5>
                <a href="{{ route('reportes.export.stock') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">Exportar CSV</a>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-secondary small text-uppercase">
                            <tr>
                                <th class="fw-semibold">Producto</th>
                                <th class="text-center fw-semibold">Stock Actual</th>
                                <th class="text-center fw-semibold">Mínimo</th>
                                <th class="text-center fw-semibold">Estado</th>
                                <th class="text-end fw-semibold">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['productos_stock_bajo'] as $prod)
                            <tr>
                                <td class="fw-medium">{{ $prod->nombre }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">{{ $prod->stock }}</span>
                                </td>
                                <td class="text-center text-muted">{{ $prod->stock_minimo }}</td>
                                <td class="text-center">
                                    @if($prod->stock == 0)
                                        <span class="badge bg-dark">Agotado</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Bajo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('productos.edit', $prod->id_producto) }}" class="btn btn-sm btn-light text-primary"><i class="bi bi-arrow-repeat"></i> Reabastecer</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Scripts para Gráficas --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';
    
    @if($rol==='admin' && isset($stats['labels_dias']))
    const ctxDias = document.getElementById('chartDias');
    if (ctxDias) {
        new Chart(ctxDias, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['labels_dias']) !!},
                datasets: [{
                    label: 'Ventas ({{ $configuracion['moneda'] ?? '$' }} )',
                    data: {!! json_encode($stats['serie_dias']) !!},
                    borderColor: '#4f46e5', // Primary Color
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '{{ $configuracion['moneda'] ?? '$' }} ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: {
                            callback: function(value) { return '{{ $configuracion['moneda'] ?? '$' }} ' + value; }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    const ctxTop = document.getElementById('chartTopProductos');
    if (ctxTop && {!! json_encode(isset($stats['top_productos']) && count($stats['top_productos']) > 0) !!}) {
        const topData = {!! json_encode($stats['top_productos'] ?? []) !!};
        new Chart(ctxTop, {
            type: 'doughnut',
            data: {
                labels: topData.map(item => item.nombre),
                datasets: [{
                    data: topData.map(item => item.total_vendido),
                    backgroundColor: [
                        '#4f46e5', '#818cf8', '#c7d2fe', '#312e81', '#6366f1'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '75%'
            }
        });
    }
    @endif

    @if($rol==='cajero' && isset($stats['labels_horas']))
    const ctxHoras = document.getElementById('chartHoras');
    if (ctxHoras) {
        new Chart(ctxHoras, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['labels_horas']) !!},
                datasets: [{
                    label: 'Ventas ({{ $configuracion['moneda'] ?? '$' }} )',
                    data: {!! json_encode($stats['serie_horas']) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: {
                            callback: function(value) { return '{{ $configuracion['moneda'] ?? '$' }} ' + value; }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    const ctxTopCajero = document.getElementById('chartTopProductosCajero');
    if (ctxTopCajero && {!! json_encode(isset($stats['mis_top_productos_hoy']) && count($stats['mis_top_productos_hoy']) > 0) !!}) {
        const topDataC = {!! json_encode($stats['mis_top_productos_hoy'] ?? []) !!};
        new Chart(ctxTopCajero, {
            type: 'doughnut',
            data: {
                labels: topDataC.map(item => item.producto),
                datasets: [{
                    data: topDataC.map(item => item.cantidad),
                    backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe', '#312e81', '#6366f1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '75%'
            }
        });
    }
    @endif
});
</script>


@endsection
