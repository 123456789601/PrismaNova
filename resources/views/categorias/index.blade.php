@extends('layouts.app')
@section('title','Categorías')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-tags-fill me-2"></i>Categorías</h4>
            <p class="text-white-50 small mb-0">Gestión de categorías de productos</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-0 rounded-start-pill">ID</th>
                            <th class="py-3 border-0">Nombre</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0 rounded-end-pill">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($categorias as $c)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $c->id_categoria }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-secondary bg-opacity-10 rounded-circle text-white me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                    <span class="fw-bold text-white">{{ $c->nombre }}</span>
                                </div>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                @if($c->estado == 'ACTIVO')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border border-success border-opacity-25">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 rounded-pill px-3 border border-secondary border-opacity-25">{{ $c->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('categorias.edit',$c) }}" class="btn btn-sm btn-outline-light text-primary border-0 hover-scale" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('categorias.destroy',$c) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-light text-danger border-0 hover-scale" onclick="return confirm('¿Eliminar?')" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-white-50">
                                    <i class="bi bi-tags fs-1 d-block mb-2 opacity-50"></i>
                                    No hay categorías registradas.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categorias->hasPages())
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3 d-flex justify-content-center">
            {{ $categorias->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
