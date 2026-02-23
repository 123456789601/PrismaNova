@extends('layouts.app')
@section('title','Editar Categoría')
@section('content')
<h4 class="mb-3">Editar Categoría</h4>
<form method="POST" action="{{ route('categorias.update',$categoria) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre',$categoria->nombre) }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion">{{ old('descripcion',$categoria->descripcion) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo" @if($categoria->estado==='activo') selected @endif>Activo</option>
                <option value="inactivo" @if($categoria->estado==='inactivo') selected @endif>Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
