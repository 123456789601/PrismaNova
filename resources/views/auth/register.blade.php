@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">
    <a href="/" class="d-flex align-items-center gap-2 text-decoration-none mb-4">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-lg logo-icon">
            <i class="bi bi-prism-fill fs-4"></i>
        </div>
        <span class="fw-bold fs-3 text-body">PrismaNova</span>
    </a>

    <div class="glass-card w-100 auth-card overflow-hidden" style="max-width: 500px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3 d-inline-block p-3 rounded-circle bg-success bg-opacity-10 text-success shadow-sm">
                    <i class="bi bi-person-plus-fill fs-1"></i>
                </div>
                <h4 class="fw-bold text-body">Crear Cuenta</h4>
                <p class="text-muted small">Regístrate para comenzar a comprar</p>
            </div>

            <form method="POST" action="{{ route('register.attempt') }}">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('nombre') }}" required autofocus placeholder="Juan" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                        </div>
                        @error('nombre')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Apellido</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-person"></i></span>
                            <input type="text" name="apellido" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('apellido') }}" required placeholder="Pérez" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                        </div>
                        @error('apellido')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Documento (DNI/RUC)</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-card-heading"></i></span>
                        <input type="text" name="documento" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('documento') }}" required placeholder="12345678" pattern="\d+" title="Solo números" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    @error('documento')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Email</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('email') }}" required placeholder="nombre@ejemplo.com">
                    </div>
                    @error('email')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" required placeholder="********" title="Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos">
                    </div>
                    <div class="form-text text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.</div>
                    @error('password')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-check-circle-fill"></i></span>
                        <input type="password" name="password_confirmation" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" required placeholder="Repetir contraseña">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm rounded-pill transform-hover hover-scale btn-gradient-success border-0">
                    Registrarse <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-light border-opacity-10">
                <p class="small text-muted mb-2">¿Ya tienes una cuenta?</p>
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill btn-sm px-4 fw-bold border-opacity-25 hover-scale">
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
