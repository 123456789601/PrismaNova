@extends('layouts.app')

@section('title', 'Bienvenido')

@section('content')
<div class="container hero-section d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="d-flex justify-content-between align-items-center w-100 py-4 mb-auto fade-in-up">
        <a href="/" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-40-px h-40-px shadow-sm">
                <i class="bi bi-prism-fill fs-5"></i>
            </div>
            <span class="fw-bold fs-4 text-body">PrismaNova</span>
        </a>
        
        <div class="d-flex align-items-center gap-3">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold d-none d-sm-block border-2">Ingresar</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">Registrarse</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="row align-items-center flex-grow-1 py-5">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="pe-lg-5">
                <h1 class="display-3 fw-bolder mb-4 fade-in-up delay-200 text-gradient">
                    Haz tu mercado fácil con <span id="typing-text" class="text-primary"></span><span class="cursor-blink">|</span>
                </h1>
                <p class="lead text-muted mb-5 fade-in-up delay-300 fs-4">
                    Todo lo que necesitas para tu hogar en un solo lugar. 
                    Calidad, frescura y rapidez directamente a tu puerta.
                </p>
                <div class="d-flex gap-3 fade-in-up delay-400 flex-wrap">
                    @auth
                        <a href="{{ url('/tienda') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold transform-hover">Ver Catálogo <i class="bi bi-bag-fill ms-2"></i></a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold transform-hover">Empezar a Comprar <i class="bi bi-cart-fill ms-2"></i></a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5 fw-bold border-2 transform-hover">Ingresar</a>
                    @endauth
                </div>
                
                <!-- Estadísticas Rápidas -->
                <div class="row mt-5 fade-in-up delay-500">
                    <div class="col-4">
                        <h3 class="fw-bold mb-0 text-primary counter" data-target="{{ $stats['clientes'] ?? 0 }}">0</h3>
                        <small class="text-secondary text-uppercase fw-bold stat-label">Clientes</small>
                    </div>
                    <div class="col-4 border-start border-end">
                        <h3 class="fw-bold mb-0 text-success counter" data-target="{{ $stats['ventas'] ?? 0 }}">0</h3>
                        <small class="text-secondary text-uppercase fw-bold stat-label">Ventas</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-0 text-info counter" data-target="{{ $stats['productos_total'] ?? 0 }}">0</h3>
                        <small class="text-secondary text-uppercase fw-bold stat-label">Productos</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="row g-4 fade-in-up delay-300">
                <div class="col-md-6 mb-4">
                    <div class="glass-card h-100 transform-hover">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3 text-primary">
                                <i class="bi bi-truck fs-2"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Entregas Rápidas</h5>
                            <p class="text-muted small mb-0">Recibe tu pedido en la puerta de tu casa en tiempo récord.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 mt-md-5">
                    <div class="glass-card h-100 transform-hover">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-flex mb-3 text-success">
                                <i class="bi bi-patch-check fs-2"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Productos Frescos</h5>
                            <p class="text-muted small mb-0">Garantizamos la mejor calidad y frescura en cada uno de nuestros productos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 mt-md-n5">
                    <div class="glass-card h-100 transform-hover">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-inline-flex mb-3 text-warning">
                                <i class="bi bi-tags-fill fs-2"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Precios Increíbles</h5>
                            <p class="text-muted small mb-0">Las mejores ofertas y promociones pensadas para tu bolsillo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="glass-card h-100 transform-hover">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-flex mb-3 text-info">
                                <i class="bi bi-shield-lock-fill fs-2"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Pagos Seguros</h5>
                            <p class="text-muted small mb-0">Diferentes métodos de pago protegidos con total seguridad.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container py-5 mb-5">
        <h2 class="text-center display-6 fw-bold mb-5 fade-in-up">Nuestros Destacados</h2>
        <div class="row g-4">
            @if(isset($productos) && $productos->count() > 0)
                @foreach($productos as $producto)
                    <div class="col-md-6 col-lg-3 fade-in-up delay-100">
                        <div class="glass-card h-100 d-flex flex-column overflow-hidden position-relative group">
                            <div class="bg-light bg-opacity-50 d-flex align-items-center justify-content-center p-4 h-200-px">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-fluid product-img-contain">
                                @else
                                    <i class="bi bi-box-seam fs-1 text-secondary opacity-50"></i>
                                @endif
                            </div>
                            <div class="p-4 flex-grow-1 d-flex flex-column">
                                <h5 class="fw-bold mb-2">{{ $producto->nombre }}</h5>
                                <p class="text-muted small flex-grow-1 mb-3">{{ Str::limit($producto->descripcion ?? 'Sin descripción', 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="fs-5 fw-bold text-primary">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($producto->precio_venta, 2, '.', ',') }}</span>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 add-to-cart-btn" data-name="{{ $producto->nombre }}">
                                        <i class="bi bi-bag-plus-fill me-1"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center fade-in-up">
                    <div class="glass-card p-5 d-inline-block">
                        <i class="bi bi-stars fs-1 text-warning mb-3"></i>
                        <p class="text-muted mb-0">Pronto verás nuestros productos aquí.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto pt-5 pb-4 border-top border-light border-opacity-10 fade-in-up delay-300">
        <div class="row gy-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <a href="/" class="d-flex align-items-center gap-2 text-decoration-none mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-30-px h-30-px shadow-sm">
                        <i class="bi bi-prism-fill fs-6"></i>
                    </div>
                    <span class="fw-bold fs-5 text-body">PrismaNova</span>
                </a>
                <p class="text-muted small">Tu supermercado de confianza para el día a día. Calidad, frescura y los mejores precios para tu familia.</p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Producto</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Características</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Precios</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Integraciones</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Actualizaciones</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Compañía</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Acerca de</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Carreras</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Blog</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Contacto</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3">Suscríbete</h6>
                <p class="small text-muted mb-3">Recibe las últimas noticias y ofertas especiales.</p>
                <form action="#" class="d-flex gap-2">
                    <input type="email" class="form-control form-control-sm rounded-pill bg-light border-0" placeholder="Tu email">
                    <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Enviar</button>
                </form>
            </div>
        </div>
        <div class="text-center text-muted small border-top border-light border-opacity-10 pt-4">
            &copy; {{ date('Y') }} PrismaNova. Todos los derechos reservados.
        </div>
    </footer>
</div>
@endsection

@section('scripts')
<script>
    // Efecto Typing en el Título
    const typingTextElement = document.getElementById('typing-text');
    const words = ["Frescura", "Calidad", "Rapidez", "Hogar"];
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typeSpeed = 100;

    function typeEffect() {
        const currentWord = words[wordIndex];
        
        if (isDeleting) {
            typingTextElement.textContent = currentWord.substring(0, charIndex - 1);
            charIndex--;
            typeSpeed = 50;
        } else {
            typingTextElement.textContent = currentWord.substring(0, charIndex + 1);
            charIndex++;
            typeSpeed = 150;
        }

        if (!isDeleting && charIndex === currentWord.length) {
            isDeleting = true;
            typeSpeed = 2000; // Pausa al completar la palabra
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            typeSpeed = 500; // Pausa antes de escribir la siguiente
        }

        setTimeout(typeEffect, typeSpeed);
    }

    // Iniciar efecto typing
    document.addEventListener('DOMContentLoaded', typeEffect);

    // Animación de Contadores
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // Cuanto más bajo, más lento

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target + "+"; // Añadir '+' al final
                }
            };
            updateCount();
        });
    };

    // Trigger de animación cuando es visible
    let animated = false;
    const statsSection = document.querySelector('.counter')?.closest('.row'); // Seleccionar el contenedor de los contadores

    function checkScroll() {
        if (statsSection && !animated) {
            const rect = statsSection.getBoundingClientRect();
            if (rect.top < window.innerHeight - 50) { // 50px de margen
                animateCounters();
                animated = true;
                window.removeEventListener('scroll', checkScroll); // Dejar de escuchar una vez animado
            }
        }
    }

    window.addEventListener('scroll', checkScroll);
    // Trigger inicial si ya es visible
    document.addEventListener('DOMContentLoaded', checkScroll);

    // Add to Cart Interaction for Guests
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productName = this.getAttribute('data-name');
            
            Swal.fire({
                title: '¡Inicia sesión!',
                text: `Para agregar "${productName}" a tu carrito, necesitas una cuenta.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Iniciar Sesión',
                cancelButtonText: 'Crear Cuenta',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#10b981',
                background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e293b' : '#fff',
                color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#1e293b',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("login") }}';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '{{ route("register") }}';
                }
            });
        });
    });
</script>
@endsection
