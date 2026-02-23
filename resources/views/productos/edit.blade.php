@extends('layouts.app')
@section('title','Editar Producto')
@section('content')
<h4 class="mb-3">Editar Producto</h4>
<form method="POST" action="{{ route('productos.update',$producto) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre',$producto->nombre) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Código de barras</label>
            <input class="form-control" name="codigo_barras" value="{{ old('codigo_barras',$producto->codigo_barras) }}">
        </div>
        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion">{{ old('descripcion',$producto->descripcion) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            @if($producto->imagen)
                <div class="mt-2">
                    <img src="{{ asset('storage/'.$producto->imagen) }}" alt="Imagen actual" class="img-thumbnail" style="max-height:120px">
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria" required>
                @foreach($categorias as $c)
                    <option value="{{ $c->id_categoria }}" @if($producto->id_categoria==$c->id_categoria) selected @endif>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Proveedor</label>
            <select class="form-select" name="id_proveedor">
                <option value="">Sin proveedor</option>
                @foreach($proveedores as $p)
                    <option value="{{ $p->id_proveedor }}" @if($producto->id_proveedor==$p->id_proveedor) selected @endif>{{ $p->nombre_empresa }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Precio compra</label>
            <input type="number" step="0.01" class="form-control" name="precio_compra" value="{{ old('precio_compra',$producto->precio_compra) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Precio venta</label>
            <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ old('precio_venta',$producto->precio_venta) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" value="{{ old('stock',$producto->stock) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Stock mínimo</label>
            <input type="number" class="form-control" name="stock_minimo" value="{{ old('stock_minimo',$producto->stock_minimo) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha vencimiento</label>
            <input type="date" class="form-control" name="fecha_vencimiento" value="{{ old('fecha_vencimiento',$producto->fecha_vencimiento) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo" @if($producto->estado==='activo') selected @endif>Activo</option>
                <option value="inactivo" @if($producto->estado==='inactivo') selected @endif>Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
