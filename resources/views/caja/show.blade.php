@extends('layouts.app')
@section('title','Detalle de Caja #'.$caja->id_caja)
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold text-primary">Detalle de Caja #{{ $caja->id_caja }}</h4>
                <a href="{{ route('caja.index') }}" class="btn btn-outline-light rounded-pill shadow-sm px-4 transform-hover hover-scale">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <div class="row g-4">
                <!-- Info Card -->
                <div class="col-md-4">
                    <div class="glass-card overflow-hidden h-100">
                        <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-info-circle me-2"></i>Información</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="small text-white-50 fw-bold text-uppercase mb-1">Apertura</label>
                                <p class="h5 fw-bold text-white"><i class="bi bi-calendar-check me-2 text-primary"></i>{{ $caja->fecha_apertura }}</p>
                            </div>
                            <div class="mb-4">
                                <label class="small text-white-50 fw-bold text-uppercase mb-1">Estado</label>
                                <div>
                                    <span class="badge rounded-pill bg-{{ $caja->estado == 'abierta' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $caja->estado == 'abierta' ? 'success' : 'white' }} border border-{{ $caja->estado == 'abierta' ? 'success' : 'secondary' }} px-3 py-2">
                                        <i class="bi bi-circle-fill me-1 small"></i>{{ ucfirst($caja->estado) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="small text-white-50 fw-bold text-uppercase mb-1">Monto Inicial</label>
                                <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                                    <p class="h4 text-success fw-bold mb-0">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($caja->monto_inicial,2) }}</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="small text-white-50 fw-bold text-uppercase mb-1">Monto Final</label>
                                <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25">
                                    <p class="h4 text-primary fw-bold mb-0">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($caja->monto_final,2) }}</p>
                                </div>
                            </div>
                            
                            @if($caja->estado==='abierta')
                            <hr class="my-4 border-light border-opacity-10">
                            <form action="{{ route('caja.cerrar',$caja) }}" method="POST" class="d-grid">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger rounded-pill shadow-sm py-2 transform-hover hover-scale" onclick="return confirm('¿Seguro que desea cerrar esta caja?')">
                                    <i class="bi bi-lock-fill me-2"></i>Cerrar Caja
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Movimientos -->
                <div class="col-md-8">
                    <div class="glass-card overflow-hidden h-100">
                        <div class="card-header bg-primary bg-opacity-10 border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-list-check me-2"></i>Movimientos</h5>
                        </div>
                        <div class="card-body p-0">
                            @if($caja->estado==='abierta')
                            <div class="p-4 bg-secondary bg-opacity-10 border-bottom border-light border-opacity-10">
                                <form method="POST" action="{{ route('caja.movimiento.store',$caja) }}">
                                    @csrf
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-white-50 text-uppercase fw-bold">Tipo</label>
                                            <select class="form-select rounded-pill border-0 shadow-sm bg-secondary bg-opacity-10 text-white" name="tipo">
                                                <option value="ingreso" class="text-dark">Ingreso (+)</option>
                                                <option value="egreso" class="text-dark">Egreso (-)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-white-50 text-uppercase fw-bold">Monto</label>
                                            <div class="input-group rounded-pill overflow-hidden shadow-sm">
                                                <span class="input-group-text border-0 bg-secondary bg-opacity-10 text-white ps-3">{{ $configuracion['moneda'] ?? '$' }} </span>
                                                <input type="number" step="0.01" name="monto" class="form-control border-0 bg-secondary bg-opacity-10 text-white placeholder-gray-400" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small text-white-50 text-uppercase fw-bold">Descripción</label>
                                            <input name="descripcion" class="form-control rounded-pill border-0 shadow-sm bg-secondary bg-opacity-10 text-white placeholder-gray-400" placeholder="Detalle del movimiento">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary w-100 rounded-pill shadow-sm transform-hover hover-scale">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-white">
                                    <thead class="bg-primary bg-opacity-10 text-white">
                                        <tr>
                                            <th class="ps-4 py-3 border-0">Fecha</th>
                                            <th class="py-3 border-0">Tipo</th>
                                            <th class="py-3 text-end border-0">Monto</th>
                                            <th class="py-3 ps-4 border-0">Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($caja->movimientos as $m)
                                        <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
                                            <td class="ps-4 text-white-50 small fw-medium">{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-opacity-10 {{ $m->tipo == 'ingreso' ? 'bg-success text-success border border-success' : 'bg-danger text-danger border border-danger' }} px-3 py-2">
                                                    {{ ucfirst($m->tipo) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold {{ $m->tipo == 'ingreso' ? 'text-success' : 'text-danger' }}">
                                                {{ $m->tipo == 'ingreso' ? '+' : '-' }} {{ $configuracion['moneda'] ?? '$' }} {{ number_format($m->monto,2) }}
                                            </td>
                                            <td class="text-white ps-4">{{ $m->descripcion }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-white-50">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                                Sin movimientos registrados
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
