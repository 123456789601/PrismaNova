@extends('layouts.app')
@section('title','Mi Perfil')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-circle me-2 text-primary"></i>Mi Perfil</h5>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success bg-success bg-opacity-10 border border-success text-white border-0 rounded-3 mb-4 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('perfil.update') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Nombre</label>
                                <input class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nombre" value="{{ old('nombre',$usuario->nombre) }}" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                                @error('nombre')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Apellido</label>
                                <input class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="apellido" value="{{ old('apellido',$usuario->apellido) }}" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                                @error('apellido')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Documento</label>
                                <input type="text" inputmode="numeric" pattern="\d+" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="documento" value="{{ old('documento',$usuario->documento) }}" required title="Solo números" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('documento')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Email</label>
                                <input type="email" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="email" value="{{ old('email',$usuario->email) }}" required>
                                @error('email')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-12 my-4">
                                <div class="d-flex align-items-center">
                                    <hr class="flex-grow-1 border-light opacity-10">
                                    <span class="px-3 fw-bold text-white-50 small text-uppercase"><i class="bi bi-shield-lock me-2"></i>Seguridad (Opcional)</span>
                                    <hr class="flex-grow-1 border-light opacity-10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Nueva contraseña</label>
                                <input type="password" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="password" placeholder="Dejar en blanco para mantener actual" minlength="8" title="Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos">
                                <div class="form-text text-white-50 small mt-1"><i class="bi bi-info-circle me-1"></i>Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.</div>
                                @error('password')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Confirmar contraseña</label>
                                <input type="password" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="password_confirmation" minlength="8">
                            </div>
                            
                            <div class="col-12 my-4">
                                <div class="d-flex align-items-center">
                                    <hr class="flex-grow-1 border-light opacity-10">
                                    <span class="px-3 fw-bold text-white-50 small text-uppercase"><i class="bi bi-geo-alt me-2"></i>Datos de Contacto</span>
                                    <hr class="flex-grow-1 border-light opacity-10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Teléfono</label>
                                <input class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="telefono" value="{{ old('telefono',$cliente->telefono ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Dirección</label>
                                <input class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="direccion" value="{{ old('direccion',$cliente->direccion ?? '') }}">
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-3 border-top border-light border-opacity-10">
                            <button class="btn btn-primary rounded-pill px-5 fw-bold hover-scale shadow-lg">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
