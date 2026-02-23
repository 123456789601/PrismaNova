@extends('layouts.app')
@section('title','Editar Cliente')
@section('content')
<h4 class="mb-3">Editar Cliente</h4>
<form method="POST" action="{{ route('clientes.update',$cliente) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre',$cliente->nombre) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input class="form-control" name="apellido" value="{{ old('apellido',$cliente->apellido) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Documento</label>
            <input class="form-control" name="documento" value="{{ old('documento',$cliente->documento) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input class="form-control" name="telefono" value="{{ old('telefono',$cliente->telefono) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input class="form-control" name="direccion" value="{{ old('direccion',$cliente->direccion) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email',$cliente->email) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo" @if($cliente->estado==='activo') selected @endif>Activo</option>
                <option value="inactivo" @if($cliente->estado==='inactivo') selected @endif>Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
