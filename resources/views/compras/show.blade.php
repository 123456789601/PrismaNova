@extends('layouts.app')
@section('title','Compra #'.$compra->id_compra)
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold text-primary">Detalle de Compra #{{ $compra->id_compra }}</h4>
                <a href="{{ route('compras.index') }}" class="btn btn-outline-light rounded-pill shadow-sm px-4 transform-hover hover-scale">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card overflow-hidden h-100">
                        <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-info-circle me-2"></i>Resumen</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Proveedor</label>
                                <div class="d-flex align-items-center p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <p class="fw-bold text-white mb-0">{{ $compra->proveedor->nombre_empresa ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Usuario</label>
                                <div class="d-flex align-items-center p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <p class="fw-bold text-white mb-0">{{ $compra->usuario->nombre ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Fecha</label>
                                <p class="mb-0 fw-bold text-white"><i class="bi bi-calendar-event me-2 text-primary"></i>{{ $compra->fecha }}</p>
                            </div>
                            <hr class="my-4 border-light border-opacity-10">
                            <div class="d-flex justify-content-between mb-2 text-white-50">
                                <span>Subtotal:</span>
                                <span class="fw-bold text-white">{{ number_format($compra->subtotal,2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-white-50">
                                <span>Impuesto:</span>
                                <span class="fw-bold text-white">{{ number_format($compra->impuesto,2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-3 border-top border-light border-opacity-10">
                                <span class="h5 fw-bold text-primary">Total:</span>
                                <span class="h4 fw-bold text-primary">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($compra->total,2) }}</span>
                            </div>
                            <div class="mt-4">
                                <span class="badge rounded-pill bg-{{ $compra->estado == 'activo' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $compra->estado == 'activo' ? 'success' : 'secondary' }} border border-{{ $compra->estado == 'activo' ? 'success' : 'secondary' }} w-100 py-2">
                                    {{ ucfirst($compra->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="glass-card overflow-hidden h-100">
                        <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-box-seam me-2"></i>Productos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-white">
                                    <thead class="bg-primary bg-opacity-10 text-white">
                                        <tr>
                                            <th class="ps-4 py-3 border-0">Producto</th>
                                            <th class="py-3 text-center border-0">Cantidad</th>
                                            <th class="py-3 text-end border-0">Precio Unitario</th>
                                            <th class="py-3 text-end pe-4 border-0">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($compra->detalles as $d)
                                        <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    @if(($d->producto->imagen ?? null))
                                                        <img src="{{ asset('storage/'.$d->producto->imagen) }}" alt="" style="width:48px;height:48px;object-fit:cover" class="rounded-3">
                                                    @else
                                                        <div class="bg-secondary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center text-white-50" style="width:48px;height:48px">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    @endif
                                                    <span class="fw-bold text-white">{{ $d->producto->nombre ?? 'N/D' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary bg-opacity-10 text-white border border-light border-opacity-10 rounded-pill px-3">{{ $d->cantidad }}</span>
                                            </td>
                                            <td class="text-end text-white-50 fw-bold">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->precio_compra,2) }}</td>
                                            <td class="text-end pe-4 fw-bold text-primary">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->subtotal,2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
