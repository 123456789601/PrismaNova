@extends('layouts.app')
@section('title','Venta #'.$venta->id_venta)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Venta #{{ $venta->id_venta }}</h4>
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? '-' }}</p>
        <p><strong>Usuario:</strong> {{ $venta->usuario->nombre ?? '-' }}</p>
        <p><strong>Fecha:</strong> {{ $venta->fecha }}</p>
        <p><strong>Subtotal:</strong> {{ number_format($venta->subtotal,2) }}</p>
        <p><strong>Descuento:</strong> {{ number_format($venta->descuento,2) }}</p>
        <p><strong>Impuesto:</strong> {{ number_format($venta->impuesto,2) }}</p>
        <p><strong>Total:</strong> {{ number_format($venta->total,2) }}</p>
        <p><strong>Método de pago:</strong> {{ $venta->metodoPago->nombre ?? $venta->metodo_pago }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</p>
    </div>
</div>
<div class="d-flex gap-2 mb-2">
    <a href="{{ route('ventas.factura',$venta) }}?print=1" target="_blank" class="btn btn-primary">Descargar PDF</a>
    <a href="{{ route('ventas.factura',$venta) }}" class="btn btn-outline-primary">Ver factura</a>
    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary d-lg-none">Volver</a>
 </div>
<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($venta->detalles as $d)
        <tr>
            <td style="width:60px">
                @if(($d->producto->imagen ?? null))
                    <img src="{{ asset('storage/'.$d->producto->imagen) }}" alt="" style="width:48px;height:48px;object-fit:cover" class="rounded">
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>{{ $d->producto->nombre ?? 'N/D' }}</td>
            <td>{{ $d->cantidad }}</td>
            <td>{{ number_format($d->precio_unitario,2) }}</td>
            <td>{{ number_format($d->subtotal,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
