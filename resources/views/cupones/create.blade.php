@extends('layouts.app')
@section('title','Nuevo Cupón')
@section('content')
<h4 class="mb-3">Nuevo Cupón</h4>
<form method="POST" action="{{ route('cupones.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Código</label>
            <input type="text" class="form-control" name="codigo" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select class="form-select" name="tipo">
                <option value="porcentaje">Porcentaje</option>
                <option value="fijo">Fijo</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Valor</label>
            <input type="number" step="0.01" class="form-control" name="valor" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Desde</label>
            <input type="datetime-local" class="form-control" name="fecha_inicio">
        </div>
        <div class="col-md-2">
            <label class="form-label">Hasta</label>
            <input type="datetime-local" class="form-control" name="fecha_fin">
        </div>
        <div class="col-md-2">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Uso máximo</label>
            <input type="number" class="form-control" name="uso_maximo">
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('cupones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
