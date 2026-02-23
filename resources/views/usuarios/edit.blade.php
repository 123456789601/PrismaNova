@extends('layouts.app')
@section('title','Editar Usuario')
@section('content')
<h4 class="mb-3">Editar Usuario</h4>
<form method="POST" action="{{ route('usuarios.update',$usuario) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre',$usuario->nombre) }}" required>
            @error('nombre')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input class="form-control" name="apellido" value="{{ old('apellido',$usuario->apellido) }}" required>
            @error('apellido')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Documento</label>
            <input class="form-control" name="documento" value="{{ old('documento',$usuario->documento) }}" required>
            @error('documento')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email',$usuario->email) }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control" name="password">
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" name="password_confirmation">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select class="form-select" name="rol" required>
                <option value="admin" @if($usuario->rol==='admin') selected @endif>Admin</option>
                <option value="cajero" @if($usuario->rol==='cajero') selected @endif>Cajero</option>
                <option value="bodeguero" @if($usuario->rol==='bodeguero') selected @endif>Bodeguero</option>
            </select>
            @error('rol')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="activo" @if($usuario->estado==='activo') selected @endif>Activo</option>
                <option value="inactivo" @if($usuario->estado==='inactivo') selected @endif>Inactivo</option>
            </select>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
@endsection
