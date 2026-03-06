@extends('layouts.app')
@section('title','Detalle de Compra')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-white"><i class="bi bi-receipt me-2 text-primary"></i>Venta #{{ $venta->id_venta }}</h4>
                    <p class="text-white-50 small mb-0">Detalle de la transacción</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('mis-compras.factura',$venta) }}?print=1" target="_blank" class="btn btn-primary rounded-pill shadow-lg transform-hover border-0">
                        <i class="bi bi-printer me-2"></i>Imprimir
                    </a>
                    <a href="{{ route('mis-compras.factura',$venta) }}" class="btn btn-outline-light rounded-pill shadow-sm border-opacity-10">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a>
                    <a href="{{ route('mis-compras.index') }}" class="btn btn-outline-light rounded-pill shadow-sm border-opacity-10">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            <div class="glass-card overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Fecha</label>
                                <p class="fw-bold text-white mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>{{ $venta->fecha }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Método de Pago</label>
                                <p class="fw-bold text-white mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>{{ $venta->metodoPago->nombre ?? $venta->metodo_pago }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Estado</label>
                                <div>
                                    <span class="badge rounded-pill px-3 py-1 {{ $venta->estado == 'COMPLETADO' ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-secondary bg-opacity-10 text-white border border-secondary' }} border-opacity-25">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 h-100">
                                <label class="small text-primary text-uppercase fw-bold mb-1">Total Pagado</label>
                                <h4 class="fw-bold text-primary mb-0">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->total,2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card overflow-hidden">
                <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-basket me-2 text-primary"></i>Productos Adquiridos</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-4 shadow-sm border border-light border-opacity-10 overflow-hidden">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="bg-primary bg-opacity-10 text-white">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Imagen</th>
                                    <th class="py-3 border-0">Producto</th>
                                    <th class="py-3 border-0">Cantidad</th>
                                    <th class="py-3 border-0">Precio Unit.</th>
                                    <th class="text-end pe-4 py-3 border-0">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach($venta->detalles as $d)
                                <tr class="hover-bg-white-10 transition-base">
                                    <td class="ps-4 border-bottom border-light border-opacity-10" style="width:80px">
                                        @if(($d->producto->imagen ?? null))
                                            <img src="{{ asset('storage/'.$d->producto->imagen) }}" alt="" style="width:48px;height:48px;object-fit:cover" class="rounded-3 shadow-sm border border-light border-opacity-10">
                                        @else
                                            <div class="bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center text-white-50 shadow-sm border border-light border-opacity-10" style="width:48px;height:48px">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="border-bottom border-light border-opacity-10">
                                        <span class="fw-bold text-white">{{ $d->producto->nombre ?? 'Producto Eliminado' }}</span>
                                    </td>
                                    <td class="border-bottom border-light border-opacity-10">
                                        <span class="badge bg-white bg-opacity-10 text-white border border-light border-opacity-10 rounded-pill px-3">{{ $d->cantidad }}</span>
                                    </td>
                                    <td class="text-white-50 border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->precio_unitario,2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-white border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($d->subtotal,2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-primary bg-opacity-10 text-white">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold py-3 border-light border-opacity-10">Subtotal:</td>
                                    <td class="text-end pe-4 py-3 border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->subtotal ?? $venta->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold py-3 border-light border-opacity-10">Impuesto:</td>
                                    <td class="text-end pe-4 py-3 border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->impuesto ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold py-3 fs-5 text-primary border-light border-opacity-10">Total:</td>
                                    <td class="text-end pe-4 py-3 fs-5 fw-bold text-primary border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->total,2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
