@extends('layouts.app')
@section('title','Registro de Cliente')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Crear cuenta de cliente</div>
            <div class="card-body">
                <form method="POST" action="{{ route('register.attempt') }}">
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
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary">Registrarme</button>
                        <a href="{{ route('login') }}" class="btn btn-link">Ya tengo cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
