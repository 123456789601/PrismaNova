@extends('layouts.app')
@section('title','Nuevo Cupón')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Crear Cupón</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('cupones.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="codigo" class="form-label fw-bold small text-white-50">Código del Cupón</label>
                            <input type="text" name="codigo" id="codigo" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" placeholder="Ej. DESCUENTO2024" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="descuento" class="form-label fw-bold small text-white-50">Porcentaje de Descuento (%)</label>
                            <input type="number" name="descuento" id="descuento" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" placeholder="Ej. 10" min="1" max="100" required>
                        </div>

                        <div class="mb-4">
                            <label for="limite_uso" class="form-label fw-bold small text-white-50">Límite de Uso (Opcional)</label>
                            <input type="number" name="limite_uso" id="limite_uso" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" placeholder="Ej. 100" min="1">
                            <div class="form-text text-white-50 opacity-75">Dejar vacío para uso ilimitado.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('cupones.index') }}" class="btn btn-outline-light rounded-pill px-4 fw-bold border-opacity-10">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm border-0">
                                <i class="bi bi-save me-2"></i>Guardar Cupón
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
