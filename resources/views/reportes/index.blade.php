@extends('layouts.app')
@section('title','Reportes')
@section('content')
<h4 class="mb-3">Reportes</h4>
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
