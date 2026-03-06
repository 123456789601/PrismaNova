@extends('layouts.app')
@section('title','Editar Producto')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Producto</h5>
                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('productos.update',$producto) }}" enctype="multipart/form-data" id="formProducto" novalidate class="row g-3">
                @csrf @method('PUT')
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('nombre') is-invalid @enderror" name="nombre" id="nombre" value="{{ old('nombre',$producto->nombre) }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Código de barras</label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('codigo_barras') is-invalid @enderror" name="codigo_barras" id="codigo_barras" value="{{ old('codigo_barras',$producto->codigo_barras) }}" pattern="[a-zA-Z0-9]+" title="Solo letras y números">
                    @error('codigo_barras')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="invalid-feedback">Código inválido (solo letras y números).</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold small text-white-50">Descripción</label>
                    <textarea class="form-control rounded-4 bg-secondary bg-opacity-10 border-0 text-white @error('descripcion') is-invalid @enderror" name="descripcion" id="descripcion" rows="3">{{ old('descripcion',$producto->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Imagen</label>
                    <input type="file" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('imagen') is-invalid @enderror" name="imagen" accept="image/*">
                    @error('imagen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($producto->imagen)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$producto->imagen) }}" alt="Imagen actual" class="img-thumbnail rounded-3 shadow-sm img-thumb-120">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('id_categoria') is-invalid @enderror" name="id_categoria" required>
                        @foreach($categorias as $c)
                            <option value="{{ $c->id_categoria }}" @if(old('id_categoria', $producto->id_categoria) == $c->id_categoria) selected @endif class="text-dark">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Proveedor</label>
                    <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('id_proveedor') is-invalid @enderror" name="id_proveedor">
                        <option value="" class="text-dark">Sin proveedor</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id_proveedor }}" @if(old('id_proveedor', $producto->id_proveedor) == $p->id_proveedor) selected @endif class="text-dark">{{ $p->nombre_empresa }}</option>
                        @endforeach
                    </select>
                    @error('id_proveedor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Precio compra <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text rounded-start-pill bg-secondary bg-opacity-10 border-0 text-white">{{ $configuracion['moneda'] ?? '$' }} </span>
                        <input type="number" step="0.01" min="0" class="form-control rounded-end-pill bg-secondary bg-opacity-10 border-0 text-white @error('precio_compra') is-invalid @enderror" name="precio_compra" value="{{ old('precio_compra',$producto->precio_compra) }}" required>
                        @error('precio_compra')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Precio venta <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text rounded-start-pill bg-secondary bg-opacity-10 border-0 text-white">{{ $configuracion['moneda'] ?? '$' }} </span>
                        <input type="number" step="0.01" min="0" class="form-control rounded-end-pill bg-secondary bg-opacity-10 border-0 text-white @error('precio_venta') is-invalid @enderror" name="precio_venta" value="{{ old('precio_venta',$producto->precio_venta) }}" required>
                        @error('precio_venta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Stock <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock',$producto->stock) }}" required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Stock mínimo</label>
                    <input type="number" min="0" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('stock_minimo') is-invalid @enderror" name="stock_minimo" value="{{ old('stock_minimo',$producto->stock_minimo) }}">
                    @error('stock_minimo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Fecha vencimiento</label>
                    <input type="date" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('fecha_vencimiento') is-invalid @enderror" name="fecha_vencimiento" value="{{ old('fecha_vencimiento',$producto->fecha_vencimiento) }}">
                    @error('fecha_vencimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-white-50">Estado <span class="text-danger">*</span></label>
                    <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white @error('estado') is-invalid @enderror" name="estado" required>
                        <option value="activo" @if(old('estado', $producto->estado)==='activo') selected @endif class="text-dark">Activo</option>
                        <option value="inactivo" @if(old('estado', $producto->estado)==='inactivo') selected @endif class="text-dark">Inactivo</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 text-end mt-4">
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-light rounded-pill px-4 me-2 shadow-sm hover-scale">Cancelar</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm hover-scale">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Protección de Consola
    console.log("%c¡DETENTE!", "color: red; font-size: 50px; font-weight: bold; -webkit-text-stroke: 1px black;");
    console.log("%cEsta función es para desarrolladores. No copies ni pegues nada aquí.", "font-size: 18px;");

    // 2. Validaciones JS
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formProducto');
        
        // Validación Código Barras (Alfanumérico)
        const codigoInput = document.getElementById('codigo_barras');
        if(codigoInput) {
            codigoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
            });
        }

        // Validación Bootstrap
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>
@endsection
