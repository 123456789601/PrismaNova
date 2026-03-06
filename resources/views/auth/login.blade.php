@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">
    <a href="/" class="d-flex align-items-center gap-2 text-decoration-none mb-4">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-lg logo-icon">
            <i class="bi bi-prism-fill fs-4"></i>
        </div>
        <span class="fw-bold fs-3 text-body">PrismaNova</span>
    </a>

    <div class="glass-card w-100 auth-card overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3 d-inline-block p-3 rounded-circle bg-primary bg-opacity-10 text-primary shadow-sm">
                    <i class="bi bi-person-circle fs-1"></i>
                </div>
                <h4 class="fw-bold text-body">Bienvenido de nuevo</h4>
                <p class="text-muted small">Ingresa tus credenciales para continuar</p>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Email</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('email') }}" required autofocus placeholder="nombre@ejemplo.com">
                    </div>
                    @error('email')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" required placeholder="••••••••">
                    </div>
                    @error('password')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input bg-secondary bg-opacity-10 border-light border-opacity-10" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Recordarme</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary fw-bold">¿Olvidaste tu contraseña?</a>
                </div>

                @if(config('services.recaptcha.site_key'))
                    <div class="mb-4 d-flex justify-content-center flex-column align-items-center">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill transform-hover hover-scale btn-gradient-primary border-0">
                    Ingresar <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-light border-opacity-10">
                <p class="small text-muted mb-2">¿No tienes una cuenta?</p>
                <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill btn-sm px-4 fw-bold border-opacity-25 hover-scale">
                    Crear cuenta de cliente
                </a>
            </div>
        </div>
    </div>
</div>

@if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endsection
