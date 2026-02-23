@extends('layouts.app')
@section('title','Nueva Categoría')
@section('content')
<h4 class="mb-3">Nueva Categoría</h4>
<form method="POST" action="{{ route('categorias.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre') }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion">{{ old('descripcion') }}</textarea>
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
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
