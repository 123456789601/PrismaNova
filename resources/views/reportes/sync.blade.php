@extends('layouts.app')
@section('title','Sincronización de Inventario')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-arrow-repeat me-2"></i>Sincronización de Inventario</h4>
            <p class="text-secondary small mb-0">Historial de sincronizaciones externas</p>
        </div>
        <form method="POST" action="{{ route('reportes.sync.run') }}">
            @csrf
            <button class="btn btn-primary rounded-pill shadow-lg transform-hover">
                <i class="bi bi-play-fill me-2"></i>Sincronizar ahora
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <div class="glass-card mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive rounded-4 shadow-sm border border-light border-opacity-10 overflow-hidden">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">External ID</th>
                            <th class="py-3 border-0">Producto</th>
                            <th class="py-3 border-0">Cantidad</th>
                            <th class="py-3 border-0">Aplicado</th>
                            <th class="py-3 pe-4 border-0">Creado</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($logs as $l)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="ps-4 fw-bold text-white-50">#{{ $l->id }}</td>
                            <td class="text-white-50 small">{{ $l->external_id }}</td>
                            <td>
                                @php $p = $l->payload ?? []; @endphp
                                <span class="fw-bold text-white">{{ $p['id_producto'] ?? '-' }}</span>
                                @if(isset($p['codigo_barras']))
                                    <span class="badge bg-secondary bg-opacity-10 text-white border border-light border-opacity-25 ms-2">{{ $p['codigo_barras'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3">
                                    {{ $p['cantidad'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($l->applied_at)
                                    <span class="text-success small"><i class="bi bi-check-circle me-1"></i>{{ $l->applied_at->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="text-warning small"><i class="bi bi-clock me-1"></i>Pendiente</span>
                                @endif
                            </td>
                            <td class="pe-4 text-white-50 small">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
