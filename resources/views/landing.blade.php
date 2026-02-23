<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrismaNova</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body{min-height:100vh;background:linear-gradient(120deg,#2a6f97,#00b4d8,#90e0ef);}
        .hero{padding:6rem 1rem;color:#fff;text-align:center}
        .hero h1{font-weight:700}
        .glass{background:rgba(255,255,255,.2);backdrop-filter:blur(6px);border-radius:12px}
    </style>
</head>
<body>
    <nav class="navbar navbar-dark" style="background:rgba(0,0,0,.25)">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">PrismaNova</a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-light">Ingresar</a>
                <a href="{{ route('register') }}" class="btn btn-light text-primary">Crear cuenta cliente</a>
            </div>
        </div>
    </nav>
    <section class="hero">
        <div class="container">
            <h1 class="mb-3">Gestiona ventas, compras y caja en un solo lugar</h1>
            <p class="lead mb-4">Rápido, simple y pensado para tu negocio</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary">Comenzar ahora</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Ya tengo cuenta</a>
            </div>
            <div class="row mt-5 g-3 justify-content-center">
                <div class="col-md-3">
                    <div class="p-4 glass text-white">Ventas ágiles</div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 glass text-white">Control de stock</div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 glass text-white">Caja y reportes</div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
