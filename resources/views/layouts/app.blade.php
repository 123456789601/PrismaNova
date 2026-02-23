<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','PRISMANOVA')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body{background:#f5f7fb}
        .navbar{background:linear-gradient(90deg,#2a6f97,#00b4d8)}
        .list-group-item-action.active,.list-group-item-action:active{background:#2a6f97;border-color:#2a6f97}
        .card.shadow-sm{border-radius:10px}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">PRISMANOVA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
            </ul>
            <div class="d-flex">
                @auth
                    <span class="navbar-text me-3">{{ auth()->user()->nombre }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Ingresar</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm text-primary">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </div>
    </nav>
<div class="container-fluid">
    <div class="row">
        @auth
            <aside class="col-md-2 bg-light min-vh-100 p-0">
                @include('partials.sidebar')
            </aside>
            <main class="col-md-10 p-4">
        @else
            <main class="col-12 p-4">
        @endauth
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
