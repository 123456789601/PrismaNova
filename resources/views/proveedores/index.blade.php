@extends('layouts.app')
@section('title','Proveedores')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-building me-2"></i>Proveedores</h4>
            <p class="text-white-50 small mb-0">Gestión de proveedores y socios</p>
        </div>
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Proveedor
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-0 rounded-start-pill">ID</th>
                            <th class="py-3 border-0">Empresa</th>
                            <th class="py-3 border-0">NIT</th>
                            <th class="py-3 border-0">Contacto</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0 rounded-end-pill">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($proveedores as $p)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 fw-bold text-white-50 border-0">#{{ $p->id_proveedor }}</td>
                            <td class="border-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial rounded-circle bg-secondary bg-opacity-10 text-white fw-bold me-3 d-flex justify-content-center align-items-center" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                        {{ substr($p->nombre_empresa, 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-white">{{ $p->nombre_empresa }}</span>
                                </div>
                            </td>
                            <td class="border-0"><span class="badge bg-secondary bg-opacity-10 text-white border border-secondary border-opacity-10 rounded-pill">{{ $p->nit }}</span></td>
                            <td class="border-0">
                                <div class="small text-white fw-medium">{{ $p->contacto }}</div>
                                @if($p->telefono)
                                <div class="small text-white-50"><i class="bi bi-telephone me-1"></i>{{ $p->telefono }}</div>
                                @endif
                            </td>
                            <td class="border-0">
                                @if($p->estado == 'ACTIVO')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border border-success border-opacity-25">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 rounded-pill px-3 border border-secondary border-opacity-25">{{ $p->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('proveedores.edit',$p) }}" class="btn btn-sm btn-outline-light text-primary border-0 hover-scale" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('proveedores.destroy',$p) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="text-white-50">
                                    <i class="bi bi-building fs-1 d-block mb-2 opacity-50"></i>
                                    No hay proveedores registrados.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($proveedores->hasPages())
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3 d-flex justify-content-center">
            {{ $proveedores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
