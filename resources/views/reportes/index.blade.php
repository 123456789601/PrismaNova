@extends('layouts.app')
@section('title','Reportes')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-bar-chart-line me-2"></i>Reportes</h4>
            <p class="text-secondary small mb-0">Análisis y estadísticas de ventas</p>
        </div>
    </div>

    <div class="glass-card mb-4 overflow-hidden">
        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-filter me-2 text-primary"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reportes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Desde</label>
                    <input type="date" name="desde" value="{{ $desde ?? '' }}" class="form-control rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta ?? '' }}" class="form-control rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Cajero</label>
                    <select name="cajero_id" class="form-select rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10">
                        <option value="" class="text-dark">Todos</option>
                        @foreach($cajeros as $cj)
                        <option value="{{ $cj->id_usuario }}" {{ ($cajero_id??'')==$cj->id_usuario?'selected':'' }} class="text-dark">{{ $cj->nombre }} {{ $cj->apellido }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Estado</label>
                    <select name="estado" class="form-select rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10">
                        <option value="" {{ !$estado?'selected':'' }} class="text-dark">Todos</option>
                        <option value="pendiente" {{ ($estado??'')==='pendiente'?'selected':'' }} class="text-dark">Pendiente</option>
                        <option value="completada" {{ ($estado??'')==='completada'?'selected':'' }} class="text-dark">Completada</option>
                        <option value="enviada" {{ ($estado??'')==='enviada'?'selected':'' }} class="text-dark">Enviada</option>
                        <option value="entregada" {{ ($estado??'')==='entregada'?'selected':'' }} class="text-dark">Entregada</option>
                        <option value="anulada" {{ ($estado??'')==='anulada'?'selected':'' }} class="text-dark">Anulada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Método de pago</label>
                    <select name="metodo_pago_id" class="form-select rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10">
                        <option value="" class="text-dark">Todos</option>
                        @foreach($metodosPago as $mp)
                        <option value="{{ $mp->id_metodo_pago }}" {{ ($metodo_pago_id??'')==$mp->id_metodo_pago?'selected':'' }} class="text-dark">{{ $mp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-white-50 text-uppercase fw-bold">Cliente</label>
                    <input type="text" name="cliente" value="{{ $cliente ?? '' }}" class="form-control rounded-pill bg-secondary bg-opacity-10 text-white border-light border-opacity-10" placeholder="Nombre o documento">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1 transform-hover shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="bi bi-search me-2"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-light d-flex align-items-center justify-content-center border-light border-opacity-25 shadow-sm" style="width: 38px; height: 38px;" title="Limpiar">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
        <div class="card-footer p-3 d-flex justify-content-end gap-2 border-top border-light border-opacity-10">
             <a href="{{ route('reportes.export.ventas', request()->query()) }}" class="btn btn-success transform-hover shadow-sm border-0">
                <i class="bi bi-file-earmark-excel me-2"></i>Exportar Ventas CSV
            </a>
            <a href="{{ route('reportes.export.stock') }}" class="btn btn-danger transform-hover shadow-sm border-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Stock Bajo CSV
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary shadow-sm">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Ventas Hoy</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($ventasHoy,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3 text-danger shadow-sm">
                        <i class="bi bi-cart-dash fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Compras Hoy</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($comprasHoy,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success shadow-sm">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Ventas Mes</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($ventasMes,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning shadow-sm">
                        <i class="bi bi-bag-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Compras Mes</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($comprasMes,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info shadow-sm">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1"># Ventas Mes</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ number_format($ventasCountMes) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card h-100 transform-hover border-0 shadow-lg">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3 text-secondary shadow-sm">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Clientes Activos</h6>
                        <h4 class="mb-0 fw-bold text-white">{{ number_format($clientesConCompraMes) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card h-100 overflow-hidden shadow-lg">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                    <h6 class="m-0 fw-bold text-white"><i class="bi bi-trophy me-2"></i>Top Productos del Mes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="bg-secondary bg-opacity-10 text-white">
                                <tr>
                                    <th class="ps-4 border-0">Producto</th>
                                    <th class="border-0">Cantidad</th>
                                    <th class="text-end pe-4 border-0">Total</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($topProductosMes as $p)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="ps-4 fw-medium text-white-50">{{ $p->producto }}</td>
                                    <td class="text-white"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill border border-primary border-opacity-25">{{ number_format($p->cantidad) }}</span></td>
                                    <td class="text-end pe-4 fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($p->total,2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-white-50">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card mb-4 overflow-hidden">
        <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
            <h6 class="m-0 fw-bold text-white"><i class="bi bi-list-check me-2"></i>Detalle de Ventas (Rango Seleccionado)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">Fecha</th>
                            <th class="py-3 border-0">Cliente</th>
                            <th class="py-3 border-0">Cajero</th>
                            <th class="py-3 border-0">Total</th>
                            <th class="py-3 border-0">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($ventasList as $v)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="ps-4 fw-bold text-white-50">#{{ $v->id_venta }}</td>
                            <td class="text-white-50">{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }}</td>
                            <td class="text-white">{{ $v->cliente->nombre ?? '-' }}</td>
                            <td class="text-white-50">{{ $v->usuario->nombre ?? '-' }}</td>
                            <td class="fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($v->total,2) }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $v->estado=='completada'?'success':($v->estado=='pendiente'?'warning':'danger') }} bg-opacity-10 text-{{ $v->estado=='completada'?'success':($v->estado=='pendiente'?'warning':'danger') }} px-3 border border-{{ $v->estado=='completada'?'success':($v->estado=='pendiente'?'warning':'danger') }} border-opacity-25">
                                    {{ ucfirst($v->estado) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-white-50">No se encontraron ventas en este rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 p-4 glass-card">
        <div>
            <h6 class="fw-bold mb-1 text-white"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Sincronización de Inventario</h6>
            <p class="text-white-50 small mb-0">Administra y ejecuta sincronizaciones con sistemas externos</p>
        </div>
        <a href="{{ route('reportes.sync') }}" class="btn btn-outline-primary rounded-pill px-4 transform-hover hover-scale">
            Ver Sync de Inventario <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>

    @if(isset($ultimaSync) && $ultimaSync)
        <div class="alert alert-info mt-3 rounded-4 border-0 d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <strong>Última Sincronización:</strong> {{ $ultimaSync }}
            </div>
        </div>
    @endif
</div>
@endsection
