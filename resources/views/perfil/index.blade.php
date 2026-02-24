@extends('layouts.app')
@section('title','Mi Perfil')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Mi Perfil</h4>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
 </div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('perfil.update') }}">
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
            <input type="text" inputmode="numeric" pattern="\d*" class="form-control" name="documento" value="{{ old('documento',$usuario->documento) }}" required>
            @error('documento')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email',$usuario->email) }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control" name="password">
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" name="password_confirmation">
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input class="form-control" name="telefono" value="{{ old('telefono',$cliente->telefono ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input class="form-control" name="direccion" value="{{ old('direccion',$cliente->direccion ?? '') }}">
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary">Guardar cambios</button>
    </div>
</form>
@endsection
