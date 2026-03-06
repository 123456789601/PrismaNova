@extends('layouts.app')

@section('title','Papelera de Productos')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-trash me-2"></i>Papelera de Reciclaje</h4>
            <p class="text-secondary small mb-0">Gestión de productos eliminados</p>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-3 transform-hover">
            <i class="bi bi-arrow-left me-1"></i>Volver al catálogo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive overflow-hidden">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-bottom border-light border-opacity-10">Imagen</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Nombre / Código</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Categoría</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Fecha Eliminación</th>
                            <th class="text-end pe-4 py-3 border-bottom border-light border-opacity-10">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($productos as $p)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 border-bottom border-light border-opacity-10" style="width:70px">
                                 @if($p->imagen_url)
                                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" style="width:48px;height:48px;object-fit:cover" class="rounded-3 shadow-sm img-thumb-48">
                                @else
                                    <div class="bg-secondary bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white-50 small shadow-sm" style="width:48px;height:48px">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="fw-bold text-white">{{ $p->nombre }}</div>
                                <small class="text-white-50"><i class="bi bi-upc me-1"></i>{{ $p->codigo_barras }}</small>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">
                                    {{ $p->categoria->nombre ?? '-' }}
                                </span>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center text-white-50">
                                    <i class="bi bi-calendar-x me-2"></i>
                                    <div>
                                        <span class="d-block">{{ $p->deleted_at->format('d/m/Y') }}</span>
                                        <small class="opacity-75">{{ $p->deleted_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('productos.restaurar', $p->id_producto) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 hover-scale" title="Restaurar">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('productos.forceDelete', $p->id_producto) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 hover-scale" onclick="return confirm('¿Estás seguro? Esta acción NO se puede deshacer y eliminará el producto permanentemente si no tiene registros asociados.')" title="Eliminar permanentemente">
                                            <i class="bi bi-x-circle me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="avatar-xxl rounded-circle bg-secondary bg-opacity-10 text-white-50 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-recycle fs-1"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted fw-bold">Papelera Vacía</h5>
                                <p class="text-secondary small mb-0">No hay productos eliminados recientemente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($productos->hasPages())
        <div class="card-footer bg-transparent border-top py-3">
            {{ $productos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
