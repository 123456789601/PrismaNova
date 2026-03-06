@extends('layouts.app')
@section('title','Nuevo Usuario')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent py-3 border-bottom border-light border-opacity-10 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h5>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('usuarios.store') }}" id="formUsuario" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nombre" id="nombre" value="{{ old('nombre') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')" title="Solo letras y espacios">
                                @error('nombre')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                <div class="invalid-feedback ms-2">Nombre requerido (solo letras).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="apellido" id="apellido" value="{{ old('apellido') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')" title="Solo letras y espacios">
                                @error('apellido')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                <div class="invalid-feedback ms-2">Apellido requerido (solo letras).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Documento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="documento" id="documento" value="{{ old('documento') }}" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="Solo números">
                                @error('documento')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                <div class="invalid-feedback ms-2">Documento requerido (solo números).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="email" id="email" value="{{ old('email') }}" required>
                                @error('email')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                <div class="invalid-feedback ms-2">Email inválido.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="password" required minlength="8" title="Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos">
                                <div class="form-text text-white-50 small mt-1"><i class="bi bi-info-circle me-1"></i>Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.</div>
                                @error('password')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                <div class="invalid-feedback ms-2">Contraseña requerida (min 8 caracteres).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Confirmar contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="password_confirmation" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Rol <span class="text-danger">*</span></label>
                                <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="rol_id" required>
                                    <option value="" class="bg-dark text-white">Seleccione</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" class="bg-dark text-white" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                            {{ ucfirst($rol->nombre) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol_id')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-white-50">Estado <span class="text-danger">*</span></label>
                                <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="estado" required>
                                    <option value="activo" class="bg-dark text-white">Activo</option>
                                    <option value="inactivo" class="bg-dark text-white">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light rounded-pill px-4 fw-bold shadow-sm hover-scale">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-scale">
                                <i class="bi bi-save me-2"></i>Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // 1. Protección de Consola
    console.log("%c¡DETENTE!", "color: red; font-size: 50px; font-weight: bold; -webkit-text-stroke: 1px black;");
    console.log("%cEsta función es para desarrolladores. No copies ni pegues nada aquí.", "font-size: 18px;");

    // 2. Validaciones JS
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formUsuario');
        
        // Validación Nombres (Solo letras)
        const textInputs = ['nombre', 'apellido'];
        textInputs.forEach(id => {
            const input = document.getElementById(id);
            if(input) {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
                });
            }
        });

        // Validación Documento (Solo números)
        const docInput = document.getElementById('documento');
        if(docInput) {
            docInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Validación Bootstrap
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>
