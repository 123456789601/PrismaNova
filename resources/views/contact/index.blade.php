@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
                    <h4 class="mb-0 fw-bold text-white"><i class="bi bi-envelope-open me-2 text-primary"></i>Contáctanos</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-white" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-25 text-white" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-5">
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                        {{-- Admin View: Edit Contact Info --}}
                        <div class="col-md-6">
                            <h5 class="mb-4 text-white"><i class="bi bi-pencil-square me-2 text-warning"></i>Editar Información de Contacto</h5>
                            <p class="text-white-50 mb-4 small">Modifica aquí los datos de contacto y soporte que ven los clientes.</p>
                            
                            <form action="{{ route('configuracion.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="whatsapp_soporte" class="form-label text-white-50 small fw-bold text-uppercase">Número de WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-25 text-white border-0"><i class="bi bi-whatsapp"></i></span>
                                        <input type="text" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="whatsapp_soporte" name="whatsapp_soporte" value="{{ $configuracion['whatsapp_soporte'] ?? '' }}" placeholder="Ej: 573001234567">
                                    </div>
                                    <div class="form-text text-white-50 small">Ingresa el número con código de país, sin el símbolo +</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email_soporte" class="form-label text-white-50 small fw-bold text-uppercase">Email de Soporte</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-25 text-white border-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="email_soporte" name="email_soporte" value="{{ $configuracion['email_soporte'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono_contacto" class="form-label text-white-50 small fw-bold text-uppercase">Teléfono General</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-25 text-white border-0"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="telefono_contacto" name="telefono_contacto" value="{{ $configuracion['telefono_contacto'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="email_contacto" class="form-label text-white-50 small fw-bold text-uppercase">Email General</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-25 text-white border-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="email_contacto" name="email_contacto" value="{{ $configuracion['email_contacto'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="horario_atencion" class="form-label text-white-50 small fw-bold text-uppercase">Horario de Atención</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-25 text-white border-0"><i class="bi bi-clock"></i></span>
                                        <input type="text" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="horario_atencion" name="horario_atencion" value="{{ $configuracion['horario_atencion'] ?? '' }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 shadow-sm transform-hover">
                                    <i class="bi bi-save me-2"></i>Actualizar Datos
                                </button>
                            </form>
                        </div>
                        @else
                        {{-- Client View: Contact Form --}}
                        <div class="col-md-6">
                            <h5 class="mb-4 text-white">Envíanos un mensaje</h5>
                            <form action="{{ route('contact.send') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label text-white-50">Nombre</label>
                                    <input type="text" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="name" name="name" required value="{{ auth()->user()->nombre ?? '' }} {{ auth()->user()->apellido ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label text-white-50">Email</label>
                                    <input type="email" class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="email" name="email" required value="{{ auth()->user()->email ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label text-white-50">Mensaje</label>
                                    <textarea class="form-control bg-secondary bg-opacity-10 text-white border-0 p-3" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow-sm transform-hover">
                                    <i class="bi bi-send me-2"></i>Enviar Mensaje
                                </button>
                            </form>
                        </div>
                        @endif

                        {{-- Right Side: Contact Info Display --}}
                        <div class="col-md-6 border-start border-light border-opacity-10 ps-md-5">
                            <h5 class="mb-4 text-white text-center">Otras formas de contacto</h5>
                            
                            <div class="mb-4 text-center">
                                @php
                                    $whatsapp = $configuracion['whatsapp_soporte'] ?? '573000000000';
                                    // Ensure clean number for link
                                    $whatsapp_clean = preg_replace('/[^0-9]/', '', $whatsapp);
                                @endphp
                                <a href="https://wa.me/{{ $whatsapp_clean }}?text=Hola%20PrismaNova,%20necesito%20ayuda" target="_blank" class="btn btn-success w-75 py-3 mb-3 fw-bold shadow-sm transform-hover">
                                    <i class="bi bi-whatsapp me-2"></i> Contactar por WhatsApp
                                </a>
                                <p class="text-white-50 small">{{ $configuracion['horario_atencion'] ?? 'Lunes a Viernes: 8:00 AM - 6:00 PM' }}</p>
                            </div>

                            <div class="mb-4 text-center">
                                <a href="mailto:{{ $configuracion['email_soporte'] ?? ($configuracion['email_contacto'] ?? '') }}" class="btn btn-secondary bg-opacity-25 w-75 py-3 mb-3 fw-bold shadow-sm transform-hover border-0">
                                    <i class="bi bi-envelope me-2"></i> Enviar Correo Directo
                                </a>
                            </div>

                            <div class="mt-5 pt-4 border-top border-light border-opacity-10 text-center">
                                <p class="mb-2 text-primary fw-bold text-uppercase small spacing-1">Soporte Técnico</p>
                                <p class="text-white mb-1 fs-5">{{ $configuracion['email_soporte'] ?? ($configuracion['email_contacto'] ?? 'No configurado') }}</p>
                                <p class="text-white-50 mb-0">+{{ $configuracion['whatsapp_soporte'] ?? ($configuracion['telefono_contacto'] ?? 'No configurado') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
