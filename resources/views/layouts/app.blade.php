<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PrismaNova') - Gestión Inteligente</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- NProgress -->
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css" />
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        // Precargar tema
        (function() {
            try {
                var stored = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', stored);
            } catch(e) {}
        })();
    </script>
</head>
<body>
    <!-- Formas de fondo -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="d-flex position-relative">
        <!-- Overlay para móvil -->
        <div id="sidebar-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none d-md-none" style="z-index: 999; backdrop-filter: blur(2px);" onclick="toggleSidebar()"></div>

        <!-- Barra lateral (Solo para usuarios autenticados) -->
        @auth
            @include('partials.sidebar')
        @endauth

        <!-- Contenedor del contenido principal -->
        <div class="main-content flex-grow-1 w-100 {{ auth()->check() ? '' : 'ms-0' }}" style="min-height: 100vh; overflow-x: hidden;">
            <!-- Barra de navegación superior para móvil/tema -->
            <nav class="navbar navbar-expand-lg bg-body-tertiary mb-3 rounded-3 d-flex justify-content-between align-items-center p-3 d-md-none mx-3 mt-3 shadow-sm">
                @auth
                <button class="btn btn-outline-secondary border-0" type="button" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                @endauth
                <span class="fw-bold fs-5">PrismaNova</span>
                <button id="theme-toggle-mobile" class="btn btn-light rounded-circle border shadow-sm">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
            </nav>
            
            <!-- Desktop Theme Toggle (Absolute) -->
            <div class="position-absolute top-0 end-0 p-3 d-none d-md-block" style="z-index: 10;">
                <button id="theme-toggle" class="btn btn-light rounded-circle border shadow-sm p-2 transform-hover">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
            </div>

            <div class="container-fluid px-3 px-md-4 pb-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if(sidebar) {
                sidebar.classList.toggle('show');
                if(overlay) {
                    if(sidebar.classList.contains('show')) {
                        overlay.classList.remove('d-none');
                        overlay.classList.add('d-block');
                        document.body.style.overflow = 'hidden';
                    } else {
                        overlay.classList.remove('d-block');
                        overlay.classList.add('d-none');
                        document.body.style.overflow = '';
                    }
                }
            }
        }
    </script>
</body>
    <script>
        // Función para aplicar tema (Global)
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            try {
                localStorage.setItem('theme', theme);
            } catch (e) {}
            
            // Actualizar botones
            const btns = document.querySelectorAll('#theme-toggle, #theme-toggle-mobile');
            btns.forEach(btn => {
                var icon = btn.querySelector('i');
                if (icon) {
                    if (theme === 'dark') {
                        icon.classList.remove('bi-moon-stars-fill');
                        icon.classList.add('bi-sun-fill');
                        btn.classList.replace('btn-light', 'btn-dark');
                        btn.classList.add('text-warning');
                    } else {
                        icon.classList.remove('bi-sun-fill');
                        icon.classList.add('bi-moon-stars-fill');
                        btn.classList.replace('btn-dark', 'btn-light');
                        btn.classList.remove('text-warning');
                        btn.classList.add('text-secondary');
                    }
                }
            });

            // Enviar preferencia al servidor
            try {
                var token = document.querySelector('meta[name=csrf-token]').content;
                fetch('/perfil/tema', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ tema: theme })
                }).catch(function(){});
            } catch(e){}
        }

        // Event listeners
        document.querySelectorAll('#theme-toggle, #theme-toggle-mobile').forEach(btn => {
            btn.addEventListener('click', function() {
                var current = document.documentElement.getAttribute('data-bs-theme');
                var next = current === 'dark' ? 'light' : 'dark';
                applyTheme(next);
            });
        });
        
        // Inicializar estado
        var currentTheme = document.documentElement.getAttribute('data-bs-theme');
        if(currentTheme === 'dark') {
             const btns = document.querySelectorAll('#theme-toggle, #theme-toggle-mobile');
             btns.forEach(btn => {
                var icon = btn.querySelector('i');
                if(icon) {
                    icon.classList.remove('bi-moon-stars-fill');
                    icon.classList.add('bi-sun-fill');
                    btn.classList.replace('btn-light', 'btn-dark');
                    btn.classList.add('text-warning');
                }
             });
        }

        // NProgress & Transitions
        NProgress.start();
        window.addEventListener('load', () => NProgress.done());
        
        document.addEventListener('click', e => {
            const link = e.target.closest('a');
            if (link && link.href && link.href.startsWith(window.location.origin) && !link.hasAttribute('target') && !e.ctrlKey && !e.metaKey && !link.getAttribute('href').startsWith('#') && !link.getAttribute('href').startsWith('javascript')) {
                NProgress.start();
            }
        });
        
        // Form Submit Loading Effects
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    // Si el formulario no es válido, no mostramos loading y dejamos que el navegador/script maneje el error
                    return;
                }

                const btn = form.querySelector('button[type="submit"]');
                if(btn && !btn.classList.contains('no-loading') && btn.innerText.trim().length > 0) {
                    const originalHTML = btn.innerHTML;
                    btn.dataset.originalHtml = originalHTML;
                    
                    // Use setTimeout to allow the form submission to start before disabling
                    setTimeout(() => {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
                    }, 0);

                    // Re-enable after 10s in case of error/timeout
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                        NProgress.done();
                    }, 10000);
                }
                NProgress.start();
            });
        });
    </script>
</body>
</html>