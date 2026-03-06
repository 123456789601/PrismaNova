@extends('layouts.app')

@section('title', 'Bienvenido')

@section('content')
<div class="hero-section">
    <!-- Background Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="container position-relative z-index-1">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center mb-5 fade-in-up">
                <h1 class="display-4 mb-3">Bienvenido a PrismaNova</h1>
                <p class="lead text-muted mb-5">
                    Sistema integral de gestión para tu negocio. Controla ventas, inventario, clientes y más.
                </p>
                
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-gradient btn-lg shadow-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>Ir al Dashboard
                    </a>
                @else
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-gradient btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-custom btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <!-- Features Grid -->
        <div class="row g-4 mt-4">
            <div class="col-md-4 fade-in-up delay-100">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="h5 mb-3">Gestión de Ventas</h3>
                    <p class="text-muted">
                        Registra ventas de forma rápida y sencilla. Genera tickets y mantén el control de tus ingresos.
                    </p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up delay-200">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="h5 mb-3">Control de Inventario</h3>
                    <p class="text-muted">
                        Administra tus productos, categorías y stock en tiempo real. Evita faltantes y optimiza tu almacén.
                    </p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up delay-300">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="h5 mb-3">Clientes y Usuarios</h3>
                    <p class="text-muted">
                        Gestiona tu base de datos de clientes y controla el acceso de tus empleados con roles y permisos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
