@extends('layouts.app')

@section('title', 'Bitácora de Actividades')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-journal-text me-2"></i>Bitácora de Actividades</h4>
            <p class="text-secondary small mb-0">Registro de auditoría y movimientos del sistema</p>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3">
            <h5 class="mb-0 fw-bold text-white">
                <i class="bi bi-search me-2 text-primary"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Filtros -->
            <form action="{{ route('bitacora.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="usuario_id" class="form-label small text-white-50 fw-bold text-uppercase">Usuario</label>
                    <select name="usuario_id" id="usuario_id" class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white">
                        <option value="" class="text-dark">Todos</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id_usuario }}" {{ request('usuario_id') == $u->id_usuario ? 'selected' : '' }} class="text-dark">
                                {{ $u->nombre }} {{ $u->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="accion" class="form-label small text-white-50 fw-bold text-uppercase">Acción</label>
                    <select name="accion" id="accion" class="form-select rounded-pill bg-secondary bg-opacity-10 border-0 text-white">
                        <option value="" class="text-dark">Todas</option>
                        <option value="LOGIN" {{ request('accion') == 'LOGIN' ? 'selected' : '' }} class="text-dark">LOGIN</option>
                        <option value="CREATE" {{ request('accion') == 'CREATE' ? 'selected' : '' }} class="text-dark">CREATE</option>
                        <option value="UPDATE" {{ request('accion') == 'UPDATE' ? 'selected' : '' }} class="text-dark">UPDATE</option>
                        <option value="DELETE" {{ request('accion') == 'DELETE' ? 'selected' : '' }} class="text-dark">DELETE</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="fecha" class="form-label small text-white-50 fw-bold text-uppercase">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control rounded-pill bg-secondary bg-opacity-10 border-0 text-white" value="{{ request('fecha') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm transform-hover">
                        <i class="bi bi-filter me-1"></i>Filtrar
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-3 py-3 border-0">Fecha/Hora</th>
                            <th class="py-3 border-0">Usuario</th>
                            <th class="py-3 border-0">Acción</th>
                            <th class="py-3 border-0">Tabla</th>
                            <th class="py-3 border-0">Descripción</th>
                            <th class="py-3 pe-3 border-0">IP</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($registros as $registro)
                            <tr class="hover-bg-white-10">
                                <td class="ps-3 text-white-50 small border-bottom border-light border-opacity-10">{{ $registro->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="border-bottom border-light border-opacity-10">
                                    @if($registro->usuario)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial rounded-circle bg-primary bg-opacity-10 text-primary me-2 d-flex justify-content-center align-items-center border border-primary border-opacity-10" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                                {{ substr($registro->usuario->nombre, 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold text-white" style="font-size: 0.9rem;">{{ $registro->usuario->nombre }} {{ $registro->usuario->apellido }}</span>
                                                <small class="text-white-50" style="font-size: 0.75rem;">{{ $registro->usuario->rol->nombre ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-white-50 fst-italic">Sistema / Eliminado</span>
                                    @endif
                                </td>
                                <td class="border-bottom border-light border-opacity-10">
                                    <span class="badge rounded-pill 
                                        @if($registro->accion == 'LOGIN') bg-info bg-opacity-10 text-info border border-info border-opacity-25
                                        @elseif($registro->accion == 'CREATE') bg-success bg-opacity-10 text-success border border-success border-opacity-25
                                        @elseif($registro->accion == 'UPDATE') bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25
                                        @elseif($registro->accion == 'DELETE') bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25
                                        @else bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 @endif">
                                        {{ $registro->accion }}
                                    </span>
                                </td>
                                <td class="border-bottom border-light border-opacity-10"><span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25">{{ $registro->tabla ?? '-' }}</span></td>
                                <td class="text-white-50 small border-bottom border-light border-opacity-10">{{Str::limit($registro->descripcion, 80)}}</td>
                                <td class="pe-3 text-white-50 small border-bottom border-light border-opacity-10">{{ $registro->ip }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                        No hay registros en la bitácora.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $registros->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
