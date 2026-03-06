@extends('layouts.app')
@section('title','Usuarios')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-people-fill me-2"></i>Usuarios</h4>
            <p class="text-white-50 small mb-0">Gestión de usuarios del sistema</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="color: #e0e0e0;">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-bottom border-light border-opacity-10">ID</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Nombre</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Email</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Rol</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Estado</th>
                            <th class="text-end pe-4 py-3 border-bottom border-light border-opacity-10">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $u->id_usuario }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-3 d-flex justify-content-center align-items-center avatar-sm" style="width: 36px; height: 36px;">
                                        {{ substr($u->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $u->nombre }} {{ $u->apellido }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-white-50 border-bottom border-light border-opacity-10">{{ $u->email }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                    {{ $u->rol->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                @if($u->estado == 'ACTIVO')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border border-success border-opacity-25">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 rounded-pill px-3 border border-secondary border-opacity-25">{{ $u->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('usuarios.edit',$u) }}" class="btn btn-sm btn-outline-light border-0 hover-scale" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('usuarios.destroy',$u) }}" method="POST" class="d-inline">
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
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                    No hay usuarios registrados.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usuarios->hasPages())
        <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-center">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
