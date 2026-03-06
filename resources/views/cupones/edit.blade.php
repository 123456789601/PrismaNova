@extends('layouts.app')
@section('title','Editar Cupón')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Cupón</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('cupones.update', $cupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="codigo" class="form-label fw-bold small text-white-50">Código del Cupón</label>
                            <input type="text" name="codigo" id="codigo" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" value="{{ $cupon->codigo }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="descuento" class="form-label fw-bold small text-white-50">Porcentaje de Descuento (%)</label>
                            <input type="number" name="descuento" id="descuento" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" value="{{ $cupon->descuento }}" min="1" max="100" required>
                        </div>

                        <div class="mb-4">
                            <label for="limite_uso" class="form-label fw-bold small text-white-50">Límite de Uso</label>
                            <input type="number" name="limite_uso" id="limite_uso" class="form-control form-control-lg rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white placeholder-white-50 focus-ring-primary" value="{{ $cupon->limite_uso }}" min="1">
                            <div class="form-text text-white-50 opacity-75">Dejar vacío para uso ilimitado.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('cupones.index') }}" class="btn btn-outline-light rounded-pill px-4 fw-bold border-opacity-10">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm border-0">
                                <i class="bi bi-check-lg me-2"></i>Actualizar Cupón
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
