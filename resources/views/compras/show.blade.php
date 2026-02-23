@extends('layouts.app')
@section('title','Compra #'.$compra->id_compra)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Compra #{{ $compra->id_compra }}</h4>
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre_empresa ?? '-' }}</p>
        <p><strong>Usuario:</strong> {{ $compra->usuario->nombre ?? '-' }}</p>
        <p><strong>Fecha:</strong> {{ $compra->fecha }}</p>
        <p><strong>Subtotal:</strong> {{ number_format($compra->subtotal,2) }}</p>
        <p><strong>Impuesto:</strong> {{ number_format($compra->impuesto,2) }}</p>
        <p><strong>Total:</strong> {{ number_format($compra->total,2) }}</p>
        <p><strong>Estado:</strong> {{ $compra->estado }}</p>
    </div>
</div>
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
        @foreach($compra->detalles as $d)
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
            <td>{{ number_format($d->precio_compra,2) }}</td>
            <td>{{ number_format($d->subtotal,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
