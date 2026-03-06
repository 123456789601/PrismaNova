@extends('layouts.app')
@section('title','Editar Cliente')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white">Editar Cliente</h1>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <div class="glass-card">
        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
            <h5 class="mb-0 fw-bold text-white">
                <i class="bi bi-pencil-square me-2"></i>Editar Información
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('clientes.update',$cliente) }}" id="formCliente" novalidate class="row g-3">
                @csrf @method('PUT')
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nombre" id="nombre" value="{{ old('nombre',$cliente->nombre) }}" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios">
                    @error('nombre')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                    <div class="invalid-feedback">Por favor ingrese un nombre válido (solo letras).</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="apellido" id="apellido" value="{{ old('apellido',$cliente->apellido) }}" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios">
                    @error('apellido')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                    <div class="invalid-feedback">Por favor ingrese un apellido válido (solo letras).</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="documento" id="documento" value="{{ old('documento',$cliente->documento) }}" required pattern="[0-9.\-\s]+" title="Solo números, puntos, guiones y espacios">
                    @error('documento')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                    <div class="invalid-feedback">Documento requerido (solo números, puntos, guiones y espacios).</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Teléfono</label>
                    <input type="tel" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="telefono" id="telefono" value="{{ old('telefono',$cliente->telefono) }}" pattern="[0-9+\-\s]+" title="Solo números, espacios, + y -">
                    @error('telefono')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Dirección</label>
                    <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="direccion" id="direccion" value="{{ old('direccion',$cliente->direccion) }}" maxlength="200">
                    @error('direccion')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Email</label>
                    <input type="email" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="email" id="email" value="{{ old('email',$cliente->email) }}">
                    @error('email')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                    <div class="invalid-feedback">Por favor ingrese un email válido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-white-50">Estado <span class="text-danger">*</span></label>
                    <select class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="estado" required>
                        <option value="activo" @if($cliente->estado==='activo') selected @endif class="text-dark">Activo</option>
                        <option value="inactivo" @if($cliente->estado==='inactivo') selected @endif class="text-dark">Inactivo</option>
                    </select>
                </div>

                <div class="col-12 text-end mt-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-light rounded-pill px-4 me-2 hover-scale">Cancelar</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm hover-scale">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Protección de Consola (Deterrente)
    console.log("%c¡DETENTE!", "color: red; font-size: 50px; font-weight: bold; -webkit-text-stroke: 1px black;");
    console.log("%cEsta es una función del navegador para desarrolladores. Si alguien te dijo que copiaras y pegaras algo aquí para habilitar una función o 'hackear' algo, es una estafa y podría darles acceso a tu cuenta.", "font-size: 18px;");

    // 2. Validaciones en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formCliente');
        
        // Validación de Nombre y Apellido (Solo letras)
        const textInputs = ['nombre', 'apellido'];
        textInputs.forEach(id => {
            const input = document.getElementById(id);
            if(input) {
                input.addEventListener('input', function(e) {
                    // Reemplazar cualquier caracter que no sea letra o espacio
                    this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
                });
            }
        });

        // Validación de Teléfono
        const telefonoInput = document.getElementById('telefono');
        if(telefonoInput) {
            telefonoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
            });
        }
        
        // Validación de Documento (Permitir números, puntos, guiones y espacios)
        const documentoInput = document.getElementById('documento');
        if(documentoInput) {
            documentoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9.\-\s]/g, '');
            });
        }

        // Validación de Bootstrap al enviar
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