@extends('layouts.app')
@section('title','Nuevo Proveedor')
@section('content')
<h4 class="mb-3">Nuevo Proveedor</h4>
<form method="POST" action="{{ route('proveedores.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Empresa</label>
            <input class="form-control" name="nombre_empresa" value="{{ old('nombre_empresa') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">NIT</label>
            <input class="form-control" name="nit" value="{{ old('nit') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contacto</label>
            <input class="form-control" name="contacto" value="{{ old('contacto') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input class="form-control" name="telefono" value="{{ old('telefono') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input class="form-control" name="direccion" value="{{ old('direccion') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
