@extends('layouts.app')
@section('title','Clientes')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-people me-2"></i>Clientes</h4>
            <p class="text-secondary small mb-0">Gestión de clientes del sistema</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm w-100 w-md-auto text-center">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Cliente
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
            <form action="{{ route('clientes.index') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-secondary bg-opacity-10 border-0 text-white rounded-start-pill"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control bg-secondary bg-opacity-10 border-0 text-white placeholder-light" placeholder="Buscar por nombre, apellido o documento..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-primary rounded-end-pill px-4 hover-scale">Buscar</button>
                </div>
                @if(request('q'))
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center hover-scale flex-shrink-0" style="width: 38px; height: 38px;" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-0 rounded-start-pill">ID</th>
                            <th class="py-3 border-0">Nombre</th>
                            <th class="py-3 border-0">Documento</th>
                            <th class="py-3 border-0">Teléfono</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0 rounded-end-pill">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($clientes as $c)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $c->id_cliente }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-3 d-flex justify-content-center align-items-center shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $c->nombre }} {{ $c->apellido }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="border-bottom border-light border-opacity-10"><span class="badge bg-secondary bg-opacity-10 text-white border border-light border-opacity-10 rounded-pill">{{ $c->documento }}</span></td>
                            <td class="text-white-50 border-bottom border-light border-opacity-10">{{ $c->telefono }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                @if($c->estado == 'activo')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">{{ $c->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('clientes.edit',$c) }}" class="btn btn-sm btn-outline-light text-primary border-0 hover-scale" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy',$c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-light text-danger border-0 hover-scale" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($clientes->hasPages())
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3 d-flex justify-content-center">
            {{ $clientes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
