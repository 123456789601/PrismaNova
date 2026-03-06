@extends('layouts.app')
@section('title','Mis Compras')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-bag-check me-2 text-primary"></i>Mis Compras</h4>
            <p class="text-white-50 small mb-0">Historial de tus adquisiciones</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 shadow-sm border-opacity-10">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    @if(!$cliente)
        <div class="alert alert-warning border-0 rounded-4 mb-4 d-flex align-items-center shadow-sm bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <strong class="d-block">Registro de Cliente no encontrado</strong>
                <span class="small opacity-75">No se encontró un registro de cliente asociado a tu usuario. Por favor contacta al soporte.</span>
            </div>
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive rounded-4 shadow-sm border border-light border-opacity-10 overflow-hidden">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">Fecha</th>
                            <th class="py-3 border-0">Total</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($ventas as $v)
                        <tr class="hover-bg-white-10 transition-base">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $v->id_venta }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-white bg-opacity-10 rounded-circle text-white me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <span class="text-white">{{ $v->fecha }}</span>
                                </div>
                            </td>
                            <td class="fw-bold text-white border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($v->total,2) }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <span class="badge rounded-pill px-3 py-2 bg-{{ $v->estado == 'COMPLETADO' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $v->estado == 'COMPLETADO' ? 'success' : 'white' }} border border-{{ $v->estado == 'COMPLETADO' ? 'success' : 'secondary' }} border-opacity-25">
                                    {{ ucfirst($v->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group">
                                    <a href="{{ route('mis-compras.show',$v) }}" class="btn btn-sm btn-primary rounded-start-pill shadow-sm" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('mis-compras.factura',$v) }}" class="btn btn-sm btn-outline-light rounded-end-pill shadow-sm border-start-0" title="Descargar Factura">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 border-0">
                                <div class="text-white-50">
                                    <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-50"></i>
                                    Sin compras registradas.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($ventas,'links'))
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3">
            {{ $ventas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
