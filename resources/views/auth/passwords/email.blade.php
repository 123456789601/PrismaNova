@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

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
                    <i class="bi bi-key-fill fs-1"></i>
                </div>
                <h4 class="fw-bold text-body">Recuperar Contraseña</h4>
                <p class="text-muted small">Ingresa tu email y te enviaremos un enlace</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Email</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3 text-primary rounded-start-pill border-light border-opacity-10"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control border-0 bg-secondary bg-opacity-10 text-body rounded-end-pill border-light border-opacity-10" value="{{ old('email') }}" required autofocus placeholder="nombre@ejemplo.com">
                    </div>
                    @error('email')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>

                @if(config('services.recaptcha.site_key'))
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <div class="text-danger small mt-1 d-block w-100 text-center"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill transform-hover hover-scale btn-gradient-primary border-0">
                    Enviar Enlace <i class="bi bi-send-fill ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-light border-opacity-10">
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill btn-sm px-4 fw-bold border-opacity-25 hover-scale">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Login
                </a>
            </div>
        </div>
    </div>
</div>

@if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@endsection
