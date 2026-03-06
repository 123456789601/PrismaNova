@extends('layouts.app')
@section('title','Producto #'.$producto->id_producto)
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-box-seam me-2 text-primary"></i>Detalle de Producto #{{ $producto->id_producto }}</h5>
                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-fluid rounded-4 shadow-lg mb-3 transform-hover" style="height: 300px; object-fit: cover; width: 100%;">
                            @else
                                <div class="bg-secondary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center mb-3 shadow-inner" style="height: 300px; width: 100%;">
                                    <div class="text-center text-white-50">
                                        <i class="bi bi-image display-1 mb-2"></i>
                                        <p>Sin imagen</p>
                                    </div>
                                </div>
                            @endif
                            <div class="d-grid gap-2">
                                <span class="badge rounded-pill py-2 {{ $producto->estado == 'ACTIVO' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-white-50 border border-light border-opacity-10' }}">
                                    <i class="bi bi-circle-fill me-1 small"></i>{{ $producto->estado }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <h2 class="fw-bold text-white mb-1">{{ $producto->nombre }}</h2>
                            <p class="text-white-50 mb-4"><i class="bi bi-upc-scan me-2"></i>{{ $producto->codigo_barras }}</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100 transform-hover">
                                        <label class="small text-white-50 text-uppercase fw-bold mb-1">Categoría</label>
                                        <p class="fw-bold text-white mb-0 h5"><i class="bi bi-tag me-2 text-primary"></i>{{ $producto->categoria->nombre ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100 transform-hover">
                                        <label class="small text-white-50 text-uppercase fw-bold mb-1">Proveedor</label>
                                        <p class="fw-bold text-white mb-0 h5"><i class="bi bi-building me-2 text-primary"></i>{{ $producto->proveedor->nombre_empresa ?? '-' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 h-100 transform-hover">
                                        <label class="small text-primary text-uppercase fw-bold mb-1">Precio Venta</label>
                                        <p class="fw-bold text-primary mb-0 h3">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($producto->precio_venta,2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100 transform-hover">
                                        <label class="small text-white-50 text-uppercase fw-bold mb-1">Precio Compra</label>
                                        <p class="fw-bold text-white mb-0 h4">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($producto->precio_compra,2) }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 bg-{{ $producto->stock <= $producto->stock_minimo ? 'danger' : 'success' }} bg-opacity-10 rounded-4 border border-{{ $producto->stock <= $producto->stock_minimo ? 'danger' : 'success' }} border-opacity-25 h-100 transform-hover">
                                        <label class="small text-{{ $producto->stock <= $producto->stock_minimo ? 'danger' : 'success' }} text-uppercase fw-bold mb-1">Stock Actual</label>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <p class="fw-bold text-{{ $producto->stock <= $producto->stock_minimo ? 'danger' : 'success' }} mb-0 h3">{{ $producto->stock }}</p>
                                            @if($producto->stock <= $producto->stock_minimo)
                                                <i class="bi bi-exclamation-triangle-fill text-danger fs-4" title="Stock bajo"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100 transform-hover">
                                        <label class="small text-white-50 text-uppercase fw-bold mb-1">Stock Mínimo</label>
                                        <p class="fw-bold text-white mb-0 h4">{{ $producto->stock_minimo }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
