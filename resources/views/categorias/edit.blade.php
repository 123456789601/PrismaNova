@extends('layouts.app')
@section('title','Editar Categoría')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-25 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Editar Categoría</h5>
                    <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-light bg-opacity-10 text-white border-0 rounded-pill px-3 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('categorias.update', $categoria) }}" class="row g-4" id="formCategoria" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="col-md-12">
                            <label for="nombre" class="form-label fw-bold small text-white-50">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" name="nombre" id="nombre" value="{{ old('nombre', $categoria->nombre) }}" required>
                            @error('nombre')
                                <div class="text-danger small ms-2 mt-1">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback ms-2">
                                Por favor ingrese un nombre para la categoría.
                            </div>
                        </div>

                        <div class="col-12 text-end mt-5">
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary rounded-pill px-4 me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>

@section('scripts')
<script>
    // Validación de formulario Bootstrap
    (function () {
        'use strict'
        var form = document.getElementById('formCategoria');
        
        if(form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        }
    })()
</script>
@endsection
        </div>
    </div>
</div>
@endsection
