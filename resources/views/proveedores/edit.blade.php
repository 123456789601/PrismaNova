@extends('layouts.app')
@section('title','Editar Proveedor')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-25 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Editar Proveedor</h5>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-light bg-opacity-10 text-white border-0 rounded-pill px-3 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('proveedores.update',$proveedor) }}" id="formProveedor" novalidate class="row g-3">
                        @csrf @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Empresa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nombre_empresa" id="nombre_empresa" value="{{ old('nombre_empresa',$proveedor->nombre_empresa) }}" required>
                            @error('nombre_empresa')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            <div class="invalid-feedback ms-2">Nombre de la empresa requerido.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">NIT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nit" id="nit" value="{{ old('nit',$proveedor->nit) }}" required pattern="[0-9\-\.]+" title="Solo números, guiones y puntos">
                            @error('nit')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            <div class="invalid-feedback ms-2">NIT requerido (números, guiones, puntos).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Contacto</label>
                            <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="contacto" id="contacto" value="{{ old('contacto',$proveedor->contacto) }}">
                            @error('contacto')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Teléfono</label>
                            <input type="tel" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="telefono" id="telefono" value="{{ old('telefono',$proveedor->telefono) }}" pattern="[0-9+\-\s]+">
                            @error('telefono')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Dirección</label>
                            <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="direccion" id="direccion" value="{{ old('direccion',$proveedor->direccion) }}">
                            @error('direccion')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Email</label>
                            <input type="email" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="email" id="email" value="{{ old('email',$proveedor->email) }}">
                            @error('email')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            <div class="invalid-feedback ms-2">Email inválido.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-white-50">Estado <span class="text-danger">*</span></label>
                            <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="estado" required>
                                <option value="activo" class="bg-dark text-white" @if($proveedor->estado==='activo') selected @endif>Activo</option>
                                <option value="inactivo" class="bg-dark text-white" @if($proveedor->estado==='inactivo') selected @endif>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-outline-light rounded-pill px-4 me-2 shadow-sm hover-scale">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-scale">
                                <i class="bi bi-check-lg me-2"></i>Actualizar Proveedor
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
        const form = document.getElementById('formProveedor');
        
        // Validación NIT
        const nitInput = document.getElementById('nit');
        if(nitInput) {
            nitInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9\-\.]/g, '');
            });
        }

        // Validación Teléfono
        const telefonoInput = document.getElementById('telefono');
        if(telefonoInput) {
            telefonoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
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
@endsection
