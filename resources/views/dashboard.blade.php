@extends('layouts.app')
@section('title','Dashboard')
@section('content')
{{-- Obtener el rol del usuario actual para renderizar vistas condicionales --}}
@php($rol = auth()->user()->rol ?? null)

{{-- Encabezado de bienvenida --}}
<div class="p-4 rounded-3 mb-4 text-white" style="background: linear-gradient(90deg,#2a6f97,#00b4d8);">
    <h3 class="mb-1">Bienvenido a PrismaNova</h3>
    <p class="mb-0">Gestión integral de ventas, compras, stock y caja</p>
</div>

@isset($stats)
{{-- Fila de KPIs (Indicadores Clave de Desempeño) --}}
<div class="row g-3 mb-4">
    {{-- KPIs exclusivos para ADMIN --}}
    @if($rol==='admin')
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ventas hoy</div>
                    <div class="h5 mb-0">S/ {{ number_format($stats['ventas_hoy_total'] ?? 0,2) }}</div>
                    <div class="small">{{ $stats['ventas_hoy_count'] ?? 0 }} ventas</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Compras hoy</div>
                    <div class="h5 mb-0">S/ {{ number_format($stats['compras_hoy_total'] ?? 0,2) }}</div>
                    <div class="small">{{ $stats['compras_hoy_count'] ?? 0 }} compras</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ventas mes</div>
                    <div class="h5 mb-0">S/ {{ number_format($stats['ventas_mes_total'] ?? 0,2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Compras mes</div>
                    <div class="h5 mb-0">S/ {{ number_format($stats['compras_mes_total'] ?? 0,2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Stock bajo</div>
                    <div class="h5 mb-0">{{ $stats['stock_bajo'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Última sync</div>
                    @if(!empty($stats['ultima_sync']))
                        <div class="h6 mb-0">{{ \Carbon\Carbon::parse($stats['ultima_sync'])->diffForHumans() }}</div>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($stats['ultima_sync'])->format('d/m/Y H:i') }}</div>
                        <div class="mt-2">
                            <a href="{{ route('reportes.sync') }}" class="btn btn-sm btn-outline-primary">Ver logs</a>
                        </div>
                    @else
                        <div class="h6 mb-0">—</div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- KPIs comunes para ADMIN y CAJERO --}}
    @if(in_array($rol,['admin','cajero']))
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Caja</div>
                    <div class="h5 mb-0">{{ ($stats['caja_abierta'] ?? false) ? 'Abierta' : 'Cerrada' }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPIs comunes para ADMIN y BODEGUERO --}}
    @if(in_array($rol,['admin','bodeguero']))
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Productos</div>
                    <div class="h5 mb-0">{{ $stats['productos_total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPIs exclusivos para CLIENTE --}}
    @if($rol==='cliente')
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Mis compras</div>
                    <div class="h5 mb-0">{{ $stats['mis_compras'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Gasto este mes</div>
                    <div class="h5 mb-0">S/ {{ number_format($stats['gasto_mes'] ?? 0,2) }}</div>
                </div>
            </div>
        </div>
    @endif
</div>
@endisset

{{-- Accesos rápidos a módulos principales --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background:#e8f7ff;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Clientes</div>
                        <div class="small text-muted">Gestión</div>
                    </div>
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-primary">Abrir</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background:#fff4e6;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Productos</div>
                        <div class="small text-muted">Catálogo</div>
                    </div>
                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-warning">Abrir</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background:#eaf7ea;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Ventas</div>
                        <div class="small text-muted">Operación</div>
                    </div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-success">Abrir</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background:#f7e8ff;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Compras</div>
                        <div class="small text-muted">Abastecimiento</div>
                    </div>
                    <a href="{{ route('compras.index') }}" class="btn btn-sm btn-secondary">Abrir</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Menús de navegación por rol --}}
<div class="row g-3">
    @if($rol==='admin')
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Administración</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark">Usuarios</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-dark">Reportes</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Crear usuario cliente</a>
            </div>
        </div>
    </div>
    @endif
    @if(in_array($rol,['admin','cajero']))
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">Caja y Ventas</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('ventas.create') }}" class="btn btn-success">Nueva venta</a>
                <a href="{{ route('caja.index') }}" class="btn btn-outline-success">Caja</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Crear usuario cliente</a>
            </div>
        </div>
    </div>
    @endif
    @if(in_array($rol,['admin','bodeguero']))
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Inventario y Compras</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Productos</a>
                <a href="{{ route('compras.create') }}" class="btn btn-secondary">Nueva compra</a>
                <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">Proveedores</a>
            </div>
        </div>
    </div>
    @endif
    @if($rol==='cliente')
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Mi cuenta</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('tienda.catalogo') }}" class="btn btn-primary">Tienda</a>
                <a href="{{ route('tienda.carrito') }}" class="btn btn-outline-primary">Carrito</a>
                <a href="{{ route('mis-compras.index') }}" class="btn btn-outline-primary">Mis compras</a>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Sección CAJERO: Gráfica de ventas personales --}}
@if($rol==='cajero')
<div class="row g-3 mt-3">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">Mis ventas de hoy</div>
            <div class="card-body">
                <canvas id="chartHoras" height="120"></canvas>
                <div class="mt-2">
                    <div class="small text-muted">Total: S/ {{ number_format($stats['ventas_hoy_usuario_total'] ?? 0,2) }} · Ventas: {{ $stats['ventas_hoy_usuario_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Sección ADMIN: Gráfica de ventas globales --}}
@if($rol==='admin')
<div class="row g-3 mt-3">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Ventas globales del mes</div>
            <div class="card-body">
                <canvas id="chartDias" height="120"></canvas>
                <div class="mt-2">
                    <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-dark">Ir a Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Sección ADMIN: Top Cajeros (Tabla detallada) --}}
@if($rol==='admin' && !empty($stats['top_cajeros']))
<div class="row g-3 mt-3">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Top cajeros del mes</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Cajero</th>
                                <th>Ventas</th>
                                <th>Total</th>
                                <th>% Global</th>
                                <th>Tendencia (vs mes ant.)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['top_cajeros'] as $cj)
                            <tr>
                                <td>{{ $cj['nombre'] }} {{ $cj['apellido'] }}</td>
                                <td>{{ number_format($cj['ventas']) }}</td>
                                <td>S/ {{ number_format($cj['total'],2) }}</td>
                                <td>
                                    {{-- Barra de progreso visual para el porcentaje --}}
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $cj['porcentaje'] }}%</span>
                                        <div class="progress flex-grow-1" style="height: 5px; width: 50px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $cj['porcentaje'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{-- Indicador visual de tendencia --}}
                                    @if($cj['tendencia'] > 0)
                                        <span class="text-success"><i class="bi bi-arrow-up"></i> +{{ $cj['tendencia'] }}%</span>
                                    @elseif($cj['tendencia'] < 0)
                                        <span class="text-danger"><i class="bi bi-arrow-down"></i> {{ $cj['tendencia'] }}%</span>
                                    @else
                                        <span class="text-muted">= 0%</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dashboard') }}?cajero_id={{ $cj['id_usuario'] }}" class="btn btn-sm btn-outline-dark" title="Ver gráfica">Ver</a>
                                    <a href="{{ route('reportes.index') }}?cajero_id={{ $cj['id_usuario'] }}" class="btn btn-sm btn-outline-primary" title="Ver reporte">Reporte</a>
                                    <a href="{{ route('reportes.export.ventas') }}?cajero_id={{ $cj['id_usuario'] }}" class="btn btn-sm btn-primary" title="Descargar CSV">CSV</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Sección ADMIN: Gráfica detallada por cajero seleccionado --}}
@if($rol==='admin')
<div class="row g-3 mt-3">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                <span>Ventas del cajero (mes)</span>
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2 align-items-center">
                    <select name="cajero_id" class="form-select form-select-sm" style="width:220px">
                        <option value="">Seleccione cajero</option>
                        @foreach($cajeros as $cj)
                            <option value="{{ $cj->id_usuario }}" @if(($cajero_id??'')==$cj->id_usuario) selected @endif>{{ $cj->nombre }} {{ $cj->apellido }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-outline-light">Ver</button>
                </form>
            </div>
            <div class="card-body">
                <canvas id="chartDiasCajero" height="120"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Scripts para gráficas Chart.js --}}
@if(in_array($rol,['admin','cajero']))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Gráfica de horas (solo Cajero)
    @if($rol==='cajero' && !empty($stats['labels_horas']))
    new Chart(document.getElementById('chartHoras').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['labels_horas']) !!},
            datasets: [{ label: 'Total S/', data: {!! json_encode($stats['serie_horas']) !!}, borderColor: '#198754', backgroundColor: 'rgba(25,135,84,0.2)', tension: 0.2 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    @endif

    // Gráfica de días global (Admin)
    @if($rol==='admin' && !empty($stats['labels_dias']))
    new Chart(document.getElementById('chartDias').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($stats['labels_dias']) !!},
            datasets: [{ label: 'Total S/', data: {!! json_encode($stats['serie_dias']) !!}, backgroundColor: '#343a40' }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    @endif

    // Gráfica de días por cajero (Admin)
    @if($rol==='admin' && !empty($stats['labels_dias_cajero']))
    new Chart(document.getElementById('chartDiasCajero').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['labels_dias_cajero']) !!},
            datasets: [{ label: 'Total S/', data: {!! json_encode($stats['serie_dias_cajero']) !!}, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.2)', tension: 0.2 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    @endif
});
</script>
@endif
@endsection
