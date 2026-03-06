@extends('layouts.app')
@section('title', 'Estado del Sistema')

@section('content')
<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-white mb-0"><i class="bi bi-heart-pulse me-2 text-danger"></i>Estado del Sistema</h5>
            <small class="text-white-50">Monitoreo del servidor</small>
        </div>
        <div>
            <span class="badge bg-secondary bg-opacity-25 text-white border border-light border-opacity-10 px-2 py-1">
                <i class="bi bi-clock me-1"></i> {{ now()->format('H:i') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-white mb-3 py-2 small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-25 text-white mb-3 py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Metrics Grid -->
    <div class="row g-2 mb-3">
        <!-- Database Status -->
        <div class="col-md-6 col-xl-3">
            <div class="glass-card h-100 border-start border-3 {{ $health['database'] === 'OK' ? 'border-success' : 'border-danger' }}">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <p class="text-white-50 text-uppercase small fw-bold mb-0" style="font-size: 0.7rem;">Base de Datos</p>
                            <h6 class="text-white mb-0">{{ $health['database'] === 'OK' ? 'Conectado' : 'Error' }}</h6>
                        </div>
                        <div class="rounded-circle p-1 {{ $health['database'] === 'OK' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                            <i class="bi bi-database"></i>
                        </div>
                    </div>
                    @if($health['database'] === 'OK')
                        <div class="text-white-50 small" style="font-size: 0.75rem;">
                            Tamaño: <span class="text-white">{{ $health['db_size'] ?? 'N/A' }}</span>
                        </div>
                    @else
                        <div class="text-danger small text-truncate" style="font-size: 0.75rem;" title="{{ $health['database'] }}">
                            {{ $health['database'] }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Disk Status -->
        <div class="col-md-6 col-xl-3">
            <div class="glass-card h-100 border-start border-3 border-info">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <p class="text-white-50 text-uppercase small fw-bold mb-0" style="font-size: 0.7rem;">Espacio Disco</p>
                            <h6 class="text-white mb-0">{{ $health['disk']['percent'] }}% Libre</h6>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-1 text-info">
                            <i class="bi bi-hdd"></i>
                        </div>
                    </div>
                    <div class="progress bg-secondary bg-opacity-25 mb-1" style="height: 3px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ 100 - $health['disk']['percent'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-white-50 small" style="font-size: 0.7rem;">
                        <span>{{ $health['disk']['free'] }} libres</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Status -->
        <div class="col-md-6 col-xl-3">
            <div class="glass-card h-100 border-start border-3 {{ $health['cache'] === 'OK' ? 'border-success' : 'border-danger' }}">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <p class="text-white-50 text-uppercase small fw-bold mb-0" style="font-size: 0.7rem;">Caché</p>
                            <h6 class="text-white mb-0">{{ $health['cache'] === 'OK' ? 'Activo' : 'Inactivo' }}</h6>
                        </div>
                        <div class="rounded-circle p-1 {{ $health['cache'] === 'OK' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                    </div>
                    <div class="text-white-50 small" style="font-size: 0.75rem;">
                        Driver: {{ config('cache.default') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="col-md-6 col-xl-3">
            <div class="glass-card h-100 border-start border-3 border-warning">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <p class="text-white-50 text-uppercase small fw-bold mb-0" style="font-size: 0.7rem;">Versión</p>
                            <h6 class="text-white mb-0">Laravel {{ $health['system']['laravel_version'] }}</h6>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-1 text-warning">
                            <i class="bi bi-code-square"></i>
                        </div>
                    </div>
                    <div class="text-white-50 small" style="font-size: 0.75rem;">
                        PHP {{ $health['system']['php_version'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2">
        <!-- System Details & Stats -->
        <div class="col-lg-8">
            <div class="glass-card mb-3">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-2">
                    <h6 class="mb-0 text-white small"><i class="bi bi-info-circle me-2 text-primary"></i>Detalles</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 bg-transparent table-sm" style="font-size: 0.8rem;">
                            <tbody>
                                <tr>
                                    <td class="text-white-50 ps-3 py-1">OS Servidor</td>
                                    <td class="text-white text-end pe-3 py-1">{{ $health['system']['server_os'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-white-50 ps-3 py-1">IP</td>
                                    <td class="text-white text-end pe-3 py-1">{{ $health['system']['server_ip'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-white-50 ps-3 py-1">Usuarios</td>
                                    <td class="text-white text-end pe-3 py-1">{{ number_format($health['counts']['users'] ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-white-50 ps-3 py-1">Productos</td>
                                    <td class="text-white text-end pe-3 py-1">{{ number_format($health['counts']['products'] ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-white-50 ps-3 py-1">Ventas</td>
                                    <td class="text-white text-end pe-3 py-1">{{ number_format($health['counts']['sales'] ?? 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="glass-card mb-3">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-2">
                    <h6 class="mb-0 text-white small"><i class="bi bi-lightning me-2 text-warning"></i>Acciones</h6>
                </div>
                <div class="card-body p-2">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.health.optimize') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-1 shadow-sm transform-hover text-start btn-sm">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-white bg-opacity-25 p-1 me-2">
                                        <i class="bi bi-broom"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Optimizar</div>
                                        <div class="small text-white-50 fw-normal" style="font-size: 0.65rem;">Limpiar caché</div>
                                    </div>
                                </div>
                            </button>
                        </form>

                        <a href="{{ route('admin.backup.download') }}" class="btn btn-success w-100 py-1 shadow-sm transform-hover text-start btn-sm">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-white bg-opacity-25 p-1 me-2">
                                    <i class="bi bi-download"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Backup BD</div>
                                    <div class="small text-white-50 fw-normal" style="font-size: 0.65rem;">Descargar SQL</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
