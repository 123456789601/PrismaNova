@extends('layouts.app')
@section('title','Compras')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-cart-check me-2"></i>Compras</h4>
            <p class="text-secondary small mb-0">Gestión de adquisiciones y proveedores</p>
        </div>
        <a href="{{ route('compras.create') }}" class="btn btn-primary rounded-pill shadow-sm transform-hover">
            <i class="bi bi-plus-lg me-2"></i>Nueva Compra
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">Proveedor</th>
                            <th class="py-3 border-0">Fecha</th>
                            <th class="py-3 border-0">Total</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($compras as $c)
                        <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
                            <td class="ps-4 fw-bold text-white-50">#{{ $c->id_compra }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-3 d-flex justify-content-center align-items-center shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <span class="fw-bold text-white">{{ $c->proveedor->nombre_empresa ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small text-white-50 fw-medium"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') }}</div>
                                <div class="small text-white-50 opacity-75">{{ \Carbon\Carbon::parse($c->fecha)->format('H:i') }}</div>
                            </td>
                            <td class="fw-bold text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($c->total,2) }}</td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pendiente' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                        'completada' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'anulada' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'
                                    ];
                                    $statusClass = $statusClasses[$c->estado] ?? 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 text-uppercase shadow-sm">
                                    {{ ucfirst($c->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('compras.show', $c) }}" class="btn btn-sm btn-outline-light border-0 hover-scale" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($c->estado!=='anulada')
                                    <form action="{{ route('compras.anular',$c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular compra?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-light text-danger border-0 hover-scale" title="Anular">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4 pb-4">
                {{ $compras->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
