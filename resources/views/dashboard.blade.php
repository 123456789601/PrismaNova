@extends('layouts.app')
@section('title','Dashboard')
@section('content')
@php($rol = auth()->user()->rol ?? null)
<div class="p-4 rounded-3 mb-4 text-white" style="background: linear-gradient(90deg,#2a6f97,#00b4d8);">
    <h3 class="mb-1">Bienvenido a PrismaNova</h3>
    <p class="mb-0">Gestión integral de ventas, compras, stock y caja</p>
</div>
@isset($stats)
<div class="row g-3 mb-4">
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
@endsection
