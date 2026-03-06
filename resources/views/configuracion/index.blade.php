@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-gear-fill me-2 text-primary"></i>Configuración del Sistema</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <form action="{{ route('configuracion.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <div class="p-0">
                                        <h6 class="fw-bold text-white-50 mb-4 text-uppercase small"><i class="bi bi-sliders me-2"></i>Variables Globales</h6>
                                        @foreach($configuraciones as $config)
                                        <div class="mb-4">
                                            <label for="{{ $config->clave }}" class="form-label fw-bold small text-white-50 text-uppercase">
                                                {{ ucfirst(str_replace('_', ' ', $config->clave)) }}
                                            </label>
                                            @if($config->tipo == 'number')
                                                <input type="number" step="0.01" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-3 p-3" id="{{ $config->clave }}" name="{{ $config->clave }}" value="{{ $config->valor }}">
                                            @elseif($config->tipo == 'email')
                                                <input type="email" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-3 p-3" id="{{ $config->clave }}" name="{{ $config->clave }}" value="{{ $config->valor }}">
                                            @else
                                                <input type="text" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-3 p-3" id="{{ $config->clave }}" name="{{ $config->clave }}" value="{{ $config->valor }}">
                                            @endif
                                            
                                            @if($config->descripcion)
                                                <div class="form-text mt-2 text-white-50"><i class="bi bi-info-circle me-1"></i>{{ $config->descripcion }}</div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end px-0 pt-3">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transform-hover">
                                            <i class="bi bi-save me-2"></i>Guardar Configuración
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="glass-card h-100 border-0 bg-primary bg-opacity-10 shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white rounded-circle p-2 me-2 shadow-sm">
                                            <i class="bi bi-info-lg"></i>
                                        </div>
                                        <h6 class="fw-bold text-white mb-0">Información Importante</h6>
                                    </div>
                                    <p class="small text-white-50 mb-3">Estas variables afectan el comportamiento global del sistema. Tenga cuidado al modificarlas.</p>
                                    <ul class="list-unstyled small text-white-50">
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                            <div><strong>Moneda:</strong> Símbolo usado en todos los reportes, vistas y tickets.</div>
                                        </li>
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                            <div><strong>Impuesto:</strong> Valor porcentual (ej: 18) para cálculos de IGV/IVA en ventas y compras.</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
