@extends('layouts.app')
@section('title','Venta #'.$venta->id_venta)
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">Venta #{{ $venta->id_venta }}</h4>
                    <p class="text-muted mb-0"><i class="bi bi-calendar-event me-2"></i>{{ $venta->fecha }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('ventas.factura',$venta) }}?print=1" target="_blank" class="btn btn-primary rounded-pill shadow-sm px-4">
                        <i class="bi bi-printer me-2"></i>Imprimir
                    </a>
                    <a href="{{ route('ventas.factura',$venta) }}" class="btn btn-outline-primary rounded-pill shadow-sm px-4">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-light rounded-pill shadow-sm px-4">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="glass-card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-cart-check me-2 text-primary"></i>Detalle de Productos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 text-white">
                                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                                        <tr>
                                            <th class="ps-4 py-3 border-0">Producto</th>
                                            <th class="py-3 text-center border-0">Cantidad</th>
                                            <th class="py-3 text-end border-0">Precio</th>
                                            <th class="py-3 text-end pe-4 border-0">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($venta->detalles as $d)
                                        <tr class="hover-bg-white-10 transition-all">
                                            <td class="ps-4 border-bottom border-light border-opacity-10">
                                                <div class="d-flex align-items-center gap-3">
                                                    @if(($d->producto->imagen ?? null))
                                                        <img src="{{ asset('storage/'.$d->producto->imagen) }}" alt="" style="width:48px;height:48px;object-fit:cover" class="rounded-3 shadow-sm img-thumb-48">
                                                    @else
                                                        <div class="bg-secondary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center shadow-sm text-white-50 small" style="width:48px;height:48px">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    @endif
                                                    <span class="fw-bold text-white">{{ $d->producto->nombre ?? 'N/D' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center border-bottom border-light border-opacity-10">
                                                <span class="badge bg-secondary bg-opacity-10 text-white border border-secondary border-opacity-25 rounded-pill px-3">{{ $d->cantidad }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-white-50 border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->precio_unitario,2) }}</td>
                                            <td class="text-end pe-4 fw-bold text-success border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->subtotal,2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-info-circle me-2 text-primary"></i>Información</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Cliente</label>
                                <div class="d-flex align-items-center p-3 rounded-4 border border-light border-opacity-10 bg-secondary bg-opacity-10">
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <p class="fw-bold text-white mb-0">{{ $venta->cliente->nombre ?? '-' }}</p>
                                        @if(isset($venta->cliente->documento))
                                        <small class="text-white-50">{{ $venta->cliente->documento }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Vendedor</label>
                                <div class="d-flex align-items-center p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <p class="fw-bold text-white mb-0">{{ $venta->usuario->nombre ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Estado</label>
                                <div class="d-grid">
                                    <span class="badge rounded-pill py-2 {{ $venta->estado == 'pagado' || $venta->estado == 'completada' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25' }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Método de Pago</label>
                                <div class="p-2 border border-light border-opacity-10 rounded-pill text-center bg-secondary bg-opacity-10 text-white-50">
                                    <i class="bi bi-credit-card me-2"></i>{{ $venta->metodoPago->nombre ?? $venta->metodo_pago }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2 opacity-75">
                                <span>Subtotal</span>
                                <span>{{ number_format($venta->subtotal,2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 opacity-75">
                                <span>Descuento</span>
                                <span>- {{ number_format($venta->descuento,2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 opacity-75">
                                <span>Impuesto</span>
                                <span>{{ number_format($venta->impuesto,2) }}</span>
                            </div>
                            <hr class="border-white opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">Total</span>
                                <span class="h3 fw-bold mb-0">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->total,2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
