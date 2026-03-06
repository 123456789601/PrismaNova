@extends('layouts.app')
@section('title','Cupones')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Cupones</h4>
            <p class="text-white-50 small mb-0">Gestión de códigos de descuento</p>
        </div>
        <div class="d-flex gap-2">
            <form class="d-flex" method="GET" action="{{ route('cupones.index') }}" style="max-width:300px;">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-secondary bg-opacity-10 text-white placeholder-white-50" placeholder="Buscar código...">
                    <button class="btn bg-secondary bg-opacity-10 text-primary border-0"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <a href="{{ route('cupones.create') }}" class="btn btn-primary rounded-pill shadow-lg transform-hover border-0">
                <i class="bi bi-plus-lg me-2"></i>Nuevo
            </a>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive rounded-4 shadow-sm border border-light border-opacity-10 overflow-hidden">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                            <tr>
                                <th class="ps-4 py-3 border-0">ID</th>
                                <th class="py-3 border-0">Código</th>
                                <th class="py-3 border-0">Tipo</th>
                                <th class="py-3 border-0">Valor</th>
                                <th class="py-3 border-0">Vigencia</th>
                                <th class="py-3 border-0">Estado</th>
                                <th class="py-3 border-0">Usos</th>
                                <th class="text-end pe-4 py-3 border-0">Acciones</th>
                            </tr>
                        </thead>
                    <tbody class="border-top-0">
                        @foreach($cupones as $c)
                        <tr class="hover-bg-white-10 transition-base">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $c->id_cupon }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2">
                                    <i class="bi bi-upc-scan me-1"></i>{{ $c->codigo }}
                                </span>
                            </td>
                            <td class="border-bottom border-light border-opacity-10"><span class="text-white-50 small fw-bold text-uppercase">{{ $c->tipo }}</span></td>
                            <td class="fw-bold text-white border-bottom border-light border-opacity-10">
                                @if($c->tipo == 'porcentaje')
                                    {{ $c->valor }}%
                                @else
                                    {{ $configuracion['moneda'] ?? '$' }} {{ number_format($c->valor, 2) }}
                                @endif
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex flex-column small">
                                    <span class="text-success"><i class="bi bi-calendar-check me-1"></i>{{ optional($c->fecha_inicio)->format('d/m/Y') }}</span>
                                    <span class="text-danger"><i class="bi bi-calendar-x me-1"></i>{{ optional($c->fecha_fin)->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <span class="badge rounded-pill px-3 py-2 bg-{{ $c->estado == 'ACTIVO' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $c->estado == 'ACTIVO' ? 'success' : 'white' }} border border-{{ $c->estado == 'ACTIVO' ? 'success' : 'secondary' }} border-opacity-25">
                                    {{ $c->estado }}
                                </span>
                            </td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2 bg-white bg-opacity-10" style="height: 6px; width: 60px;">
                                        <div class="progress-bar rounded-pill bg-primary" role="progressbar" style="width: {{ $c->uso_maximo ? ($c->usos / $c->uso_maximo * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="small text-white-50">{{ $c->usos }} @if($c->uso_maximo) / {{ $c->uso_maximo }} @endif</span>
                                </div>
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('cupones.edit',$c) }}" class="btn btn-sm btn-white bg-white bg-opacity-10 text-primary border-end border-light border-opacity-10 hover-bg-white-20" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('cupones.destroy',$c) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-white bg-white bg-opacity-10 text-danger border-0 hover-bg-white-20" onclick="return confirm('¿Eliminar cupón?')" title="Eliminar">
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
        @if(method_exists($cupones,'links'))
        <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3">
            {{ $cupones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
