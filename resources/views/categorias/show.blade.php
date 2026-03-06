@extends('layouts.app')
@section('title','Detalle de Categoría')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-tag me-2 text-primary"></i>Detalle de Categoría</h5>
                    <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-12 text-center mb-2">
                            <div class="avatar-xxl rounded-circle bg-secondary bg-opacity-10 text-white d-inline-flex align-items-center justify-content-center shadow-lg mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; border: 1px solid rgba(255,255,255,0.1);">
                                <i class="bi bi-tags"></i>
                            </div>
                            <h4 class="fw-bold text-white mb-0">{{ $categoria->nombre }}</h4>
                            <span class="badge rounded-pill mt-2 px-3 py-2 {{ $categoria->estado == 'ACTIVO' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25' }}">
                                {{ $categoria->estado }}
                            </span>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 h-100 hover-scale transition-all">
                                <label class="small text-white-50 text-uppercase fw-bold mb-1">Descripción</label>
                                <p class="fw-bold text-white mb-0">{{ $categoria->descripcion ?: 'Sin descripción' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
