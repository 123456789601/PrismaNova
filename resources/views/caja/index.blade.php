@extends('layouts.app')
@section('title','Caja')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-cash-coin me-2"></i>Caja</h4>
            <p class="text-secondary small mb-0">Gestión de turnos y movimientos</p>
        </div>
        <form action="{{ route('caja.abrir') }}" method="POST">
            @csrf
            <button class="btn btn-primary rounded-pill px-4 shadow-sm transform-hover">
                <i class="bi bi-door-open me-2"></i>Abrir Nueva Caja
            </button>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">Apertura</th>
                            <th class="py-3 border-0">Cierre</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($cajas as $c)
                        <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
                            <td class="ps-4 fw-bold text-white">#{{ $c->id_caja }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle text-primary me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <span class="text-white fw-medium">{{ $c->fecha_apertura }}</span>
                                </div>
                            </td>
                            <td>
                                @if($c->fecha_cierre)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                        <span class="text-white fw-medium">{{ $c->fecha_cierre }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25 rounded-pill px-3">En curso</span>
                                @endif
                            </td>
                            <td>
                                @if($c->estado == 'ABIERTA')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border border-success border-opacity-25">Abierta</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 border border-secondary border-opacity-25">{{ $c->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('caja.show',$c) }}" class="btn btn-sm btn-outline-light rounded-pill shadow-sm hover-scale" title="Ver detalle">
                                    <i class="bi bi-eye me-1"></i>Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-white-50">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    No hay registros de caja.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cajas->hasPages())
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3 d-flex justify-content-center">
            {{ $cajas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
