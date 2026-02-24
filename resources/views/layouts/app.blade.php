<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título dinámico de la página, por defecto PRISMANOVA -->
    <title>@yield('title','PRISMANOVA')</title>
    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Token CSRF para peticiones AJAX seguras -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        /**
         * Script de inicialización del tema (claro/oscuro).
         * Se ejecuta inmediatamente para evitar parpadeos (FOUC).
         * Prioriza la configuración del usuario autenticado, luego localStorage, y finalmente 'light'.
         */
        (function () {
            try {
                var serverTheme = "{{ auth()->check() ? (auth()->user()->tema ?? '') : '' }}";
                if (serverTheme === 'light' || serverTheme === 'dark') {
                    localStorage.setItem('theme', serverTheme);
                }
                var stored = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', stored);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>
    <style>
        /* Estilos globales y específicos del tema */
        body{background:#f5f7fb}
        .navbar{background:linear-gradient(90deg,#2a6f97,#00b4d8)}
        .list-group-item-action.active,.list-group-item-action:active{background:#2a6f97;border-color:#2a6f97}
        .card.shadow-sm{border-radius:10px}
        [data-bs-theme="dark"] body{background:#121212;color:#f8f9fa}
        [data-bs-theme="dark"] .navbar{background:linear-gradient(90deg,#000000,#343a40)}
        [data-bs-theme="dark"] .bg-light{background-color:#1e1e1e!important}
        [data-bs-theme="dark"] .card{background-color:#1e1e1e;color:#f8f9fa}
        [data-bs-theme="dark"] .table{color:#f8f9fa}
    </style>
</head>
<body>
<!-- Barra de navegación principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">PRISMANOVA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
            </ul>
            <div class="d-flex align-items-center">
                <!-- Botón de cambio de tema -->
                <button id="theme-toggle" type="button" class="btn btn-outline-light btn-sm me-2">Modo oscuro</button>
                @auth
                    <!-- Opciones para usuario autenticado -->
                    <span class="navbar-text me-3">{{ auth()->user()->nombre }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Salir</button>
                    </form>
                @else
                    <!-- Opciones para visitantes (login/registro) -->
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Ingresar</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm text-primary">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </div>
    </nav>
<!-- Contenedor principal de la aplicación -->
<div class="container-fluid">
    <div class="row">
        @auth
            <!-- Sidebar para usuarios autenticados -->
            <aside class="col-md-2 bg-light min-vh-100 p-0">
                @include('partials.sidebar')
            </aside>
            <!-- Contenido principal con margen para sidebar -->
            <main class="col-md-10 p-4">
        @else
            <!-- Contenido principal ancho completo para visitantes -->
            <main class="col-12 p-4">
        @endauth
            <!-- Mensajes flash de sesión (éxito/error) -->
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            
            <!-- Inyección de contenido de las vistas hijas -->
            @yield('content')
        </main>
    </div>
</div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 <script>
    /**
     * Lógica para el cambio de tema en tiempo de ejecución.
     * Guarda la preferencia en localStorage y en el servidor (si está autenticado).
     */
    (function () {
        function storedTheme() {
            try {
                return localStorage.getItem('theme') || 'light';
            } catch (e) {
                return 'light';
            }
        }
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            try {
                localStorage.setItem('theme', theme);
            } catch (e) {}
            var btn = document.getElementById('theme-toggle');
            if (btn) {
                btn.textContent = theme === 'dark' ? 'Modo claro' : 'Modo oscuro';
            }
            try {
                var token = document.querySelector('meta[name=csrf-token]').content;
                var isAuth = !!document.querySelector('span.navbar-text');
                if (isAuth) {
                    // Sincronización asíncrona con el servidor
                    fetch('/perfil/tema', {
                        method:'POST',
                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
                        credentials:'include',
                        body: JSON.stringify({tema: theme})
                    }).catch(function(){});
                }
            } catch(e){}
        }
        document.addEventListener('DOMContentLoaded', function () {
            applyTheme(storedTheme());
            var btn = document.getElementById('theme-toggle');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                var next = current === 'dark' ? 'light' : 'dark';
                applyTheme(next);
            });
        });
    })();
 </script>
</body>
</html>
