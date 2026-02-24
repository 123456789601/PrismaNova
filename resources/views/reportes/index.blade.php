@extends('layouts.app')
@section('title','Reportes')
@section('content')
<h4 class="mb-3">Reportes</h4>
<form method="GET" action="{{ route('reportes.index') }}" class="d-flex align-items-end gap-2 mb-3 flex-wrap">
    <div>
        <label class="form-label mb-1">Desde</label>
        <input type="date" name="desde" value="{{ $desde ?? '' }}" class="form-control form-control-sm" style="width:160px">
    </div>
    <div>
        <label class="form-label mb-1">Cajero</label>
        <select name="cajero_id" class="form-select form-select-sm" style="width:220px">
            <option value="">Todos</option>
            @foreach($cajeros as $cj)
            <option value="{{ $cj->id_usuario }}" {{ ($cajero_id??'')==$cj->id_usuario?'selected':'' }}>{{ $cj->nombre }} {{ $cj->apellido }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta ?? '' }}" class="form-control form-control-sm" style="width:160px">
    </div>
    <div>
        <label class="form-label mb-1">Estado</label>
        <select name="estado" class="form-select form-select-sm" style="width:180px">
            <option value="" {{ !$estado?'selected':'' }}>Todos</option>
            <option value="pendiente" {{ ($estado??'')==='pendiente'?'selected':'' }}>Pendiente</option>
            <option value="completada" {{ ($estado??'')==='completada'?'selected':'' }}>Completada</option>
            <option value="enviada" {{ ($estado??'')==='enviada'?'selected':'' }}>Enviada</option>
            <option value="entregada" {{ ($estado??'')==='entregada'?'selected':'' }}>Entregada</option>
            <option value="anulada" {{ ($estado??'')==='anulada'?'selected':'' }}>Anulada</option>
        </select>
    </div>
    <div>
        <label class="form-label mb-1">Método de pago</label>
        <select name="metodo_pago_id" class="form-select form-select-sm" style="width:190px">
            <option value="">Todos</option>
            @foreach($metodosPago as $mp)
            <option value="{{ $mp->id_metodo_pago }}" {{ ($metodo_pago_id??'')==$mp->id_metodo_pago?'selected':'' }}>{{ $mp->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label mb-1">Cliente</label>
        <input type="text" name="cliente" value="{{ $cliente ?? '' }}" class="form-control form-control-sm" style="width:200px" placeholder="Nombre o documento">
    </div>
    <div class="ms-auto">
        <button class="btn btn-sm btn-outline-secondary">Aplicar filtros</button>
        <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-dark">Limpiar</a>
        <a href="{{ route('reportes.export.ventas') }}?desde={{ $desde }}&hasta={{ $hasta }}@if($estado)&estado={{ $estado }}@endif@if($metodo_pago_id)&metodo_pago_id={{ $metodo_pago_id }}@endif@if($cliente)&cliente={{ urlencode($cliente) }}@endif@if($cajero_id)&cajero_id={{ $cajero_id }}@endif" class="btn btn-sm btn-primary">Exportar CSV</a>
    </div>
</form>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Ventas Hoy</h6>
                <div class="fs-4">{{ number_format($ventasHoy,2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Compras Hoy</h6>
                <div class="fs-4">{{ number_format($comprasHoy,2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Ventas del Mes</h6>
                <div class="fs-4">{{ number_format($ventasMes,2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Compras del Mes</h6>
                <div class="fs-4">{{ number_format($comprasMes,2) }}</div>
            </div>
        </div>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Ventas del Mes (conteo)</h6>
                <div class="fs-5">{{ number_format($ventasCountMes) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Clientes con compra (mes)</h6>
                <div class="fs-5">{{ number_format($clientesConCompraMes) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6>Top productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProductosMes as $p)
                            <tr>
                                <td>{{ $p->producto }}</td>
                                <td>{{ number_format($p->cantidad) }}</td>
                                <td>{{ number_format($p->total,2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-3">
    <div class="card-body">
        <h6 class="mb-2">Ventas del rango</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Cajero</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventasList as $v)
                    <tr>
                        <td>{{ $v->id_venta }}</td>
                        <td>{{ $v->fecha }}</td>
                        <td>{{ $v->cliente->nombre ?? '-' }}</td>
                        <td>{{ $v->usuario->nombre ?? '-' }}</td>
                        <td>{{ number_format($v->total,2) }}</td>
                        <td>{{ ucfirst($v->estado) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5">Sin ventas en el rango.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('reportes.sync') }}" class="btn btn-outline-primary">Ver Sync de Inventario</a>
    <small class="text-muted ms-2">Administra y ejecuta sincronizaciones</small>
</div>
@if(isset($ultimaSync) && $ultimaSync)
<div class="mt-2 text-muted">
    Última sincronización: {{ \Carbon\Carbon::parse($ultimaSync)->diffForHumans() }} ({{ \Carbon\Carbon::parse($ultimaSync)->format('d/m/Y H:i') }})
</div>
@else
<div class="mt-2 text-muted">
    Aún no se han registrado sincronizaciones.
 </div>
@endif
@endsection
