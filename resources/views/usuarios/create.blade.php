@extends('layouts.app')
@section('title','Nuevo Usuario')
@section('content')
<h4 class="mb-3">Nuevo Usuario</h4>
<form method="POST" action="{{ route('usuarios.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input class="form-control" name="apellido" value="{{ old('apellido') }}" required>
            @error('apellido')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Documento</label>
            <input type="text" inputmode="numeric" pattern="\d*" class="form-control" name="documento" value="{{ old('documento') }}" required>
            @error('documento')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" required>
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select class="form-select" name="rol" required>
                <option value="">Seleccione</option>
                <option value="admin">Admin</option>
                <option value="cajero">Cajero</option>
                <option value="bodeguero">Bodeguero</option>
            </select>
            @error('rol')<div class="text-danger small">{{ $message }}</div>@enderror
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
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
