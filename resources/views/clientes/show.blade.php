@extends('layouts.app')
@section('title','Detalle de Cliente')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-lines-fill me-2"></i>Detalle de Cliente</h5>
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-12 text-center mb-2">
                            <div class="avatar-xxl rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                <i class="bi bi-person"></i>
                            </div>
                            <h4 class="fw-bold text-white mb-0">{{ $cliente->nombre }} {{ $cliente->apellido }}</h4>
                            <p class="text-white-50 mb-0">{{ $cliente->email }}</p>
                            <span class="badge rounded-pill mt-2 px-3 py-2 {{ $cliente->estado == 'activo' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25' }}">
                                {{ ucfirst($cliente->estado) }}
                            </span>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3 bg-secondary bg-opacity-10 rounded-4 transform-hover border border-light border-opacity-10">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Documento</label>
                                <p class="fw-bold text-white mb-0"><i class="bi bi-card-heading me-2 text-primary"></i>{{ $cliente->documento }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-secondary bg-opacity-10 rounded-4 transform-hover border border-light border-opacity-10">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Teléfono</label>
                                <p class="fw-bold text-white mb-0"><i class="bi bi-telephone me-2 text-primary"></i>{{ $cliente->telefono }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="p-3 bg-secondary bg-opacity-10 rounded-4 transform-hover border border-light border-opacity-10">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Dirección</label>
                                <p class="fw-bold text-white mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>{{ $cliente->direccion }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Ventas -->
                    <div class="mt-5">
                        <h5 class="fw-bold text-white mb-3"><i class="bi bi-receipt me-2"></i>Historial de Compras</h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle border-light border-opacity-10">
                                <thead class="bg-secondary bg-opacity-10 text-uppercase text-white-50 small">
                                    <tr>
                                        <th class="border-0 rounded-start"># Venta</th>
                                        <th class="border-0">Fecha</th>
                                        <th class="border-0">Items</th>
                                        <th class="border-0">Total</th>
                                        <th class="border-0">Estado</th>
                                        <th class="border-0 rounded-end text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($ventas as $venta)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">#{{ $venta->id_venta }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $venta->fecha->format('d/m/Y') }}</div>
                                            <div class="small text-white-50">{{ $venta->fecha->format('H:i') }}</div>
                                        </td>
                                        <td>{{ $venta->detalles->count() }} productos</td>
                                        <td class="fw-bold text-success">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->total, 2) }}</td>
                                        <td>
                                            @if($venta->estado == 'completada')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Completada</span>
                                            @elseif($venta->estado == 'anulada')
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Anulada</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">{{ ucfirst($venta->estado) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-light rounded-pill" title="Ver Detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-white-50">
                                            <i class="bi bi-cart-x d-block fs-2 mb-2"></i>
                                            Este cliente aún no ha realizado compras.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $ventas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
