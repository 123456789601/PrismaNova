@extends('layouts.app')
@section('title','Editar Proveedor')
@section('content')
<h4 class="mb-3">Editar Proveedor</h4>
<form method="POST" action="{{ route('proveedores.update',$proveedor) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Empresa</label>
            <input class="form-control" name="nombre_empresa" value="{{ old('nombre_empresa',$proveedor->nombre_empresa) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">NIT</label>
            <input class="form-control" name="nit" value="{{ old('nit',$proveedor->nit) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contacto</label>
            <input class="form-control" name="contacto" value="{{ old('contacto',$proveedor->contacto) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input class="form-control" name="telefono" value="{{ old('telefono',$proveedor->telefono) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input class="form-control" name="direccion" value="{{ old('direccion',$proveedor->direccion) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email',$proveedor->email) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo" @if($proveedor->estado==='activo') selected @endif>Activo</option>
                <option value="inactivo" @if($proveedor->estado==='inactivo') selected @endif>Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
