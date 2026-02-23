@extends('layouts.app')
@section('title','Nuevo Producto')
@section('content')
<h4 class="mb-3">Nuevo Producto</h4>
<form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Código de barras</label>
            <input class="form-control" name="codigo_barras" value="{{ old('codigo_barras') }}">
        </div>
        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion">{{ old('descripcion') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria" required>
                <option value="">Seleccione</option>
                @foreach($categorias as $c)
                    <option value="{{ $c->id_categoria }}">{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Proveedor</label>
            <select class="form-select" name="id_proveedor">
                <option value="">Sin proveedor</option>
                @foreach($proveedores as $p)
                    <option value="{{ $p->id_proveedor }}">{{ $p->nombre_empresa }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Precio compra</label>
            <input type="number" step="0.01" class="form-control" name="precio_compra" value="{{ old('precio_compra') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Precio venta</label>
            <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ old('precio_venta') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" value="{{ old('stock',0) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Stock mínimo</label>
            <input type="number" class="form-control" name="stock_minimo" value="{{ old('stock_minimo',0) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha vencimiento</label>
            <input type="date" class="form-control" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
