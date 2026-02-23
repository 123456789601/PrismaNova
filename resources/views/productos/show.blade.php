@extends('layouts.app')
@section('title','Producto #'.$producto->id_producto)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Producto #{{ $producto->id_producto }}</h4>
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
    <div class="card-body">
        @if($producto->imagen)
            <div class="mb-3">
                <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-fluid" style="max-height:200px">
            </div>
        @endif
        <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
        <p><strong>Código de barras:</strong> {{ $producto->codigo_barras }}</p>
        <p><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? '-' }}</p>
        <p><strong>Proveedor:</strong> {{ $producto->proveedor->nombre_empresa ?? '-' }}</p>
        <p><strong>Precio compra:</strong> {{ number_format($producto->precio_compra,2) }}</p>
        <p><strong>Precio venta:</strong> {{ number_format($producto->precio_venta,2) }}</p>
        <p><strong>Stock:</strong> {{ $producto->stock }}</p>
        <p><strong>Stock mínimo:</strong> {{ $producto->stock_minimo }}</p>
        <p><strong>Estado:</strong> {{ $producto->estado }}</p>
    </div>
</div>
@endsection
