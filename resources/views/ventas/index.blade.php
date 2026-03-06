@extends('layouts.app')
@section('title','Ventas')
@section('content')

<div class="container-fluid py-4">
    <div class="glass-card overflow-hidden">
        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h4 class="mb-0 fw-bold text-white"><i class="bi bi-receipt me-2 text-primary"></i>Ventas</h4>
            <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm w-100 w-md-auto text-center">
                <i class="bi bi-plus-lg me-1"></i>Nueva Venta
            </a>
        </div>

        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom border-light border-opacity-10">
                <form class="row g-3 align-items-end" method="GET" action="{{ route('ventas.index') }}">
                    <div class="col-md-3">
                        <label class="form-label small text-white-50 text-uppercase fw-bold">Estado</label>
                        <select name="estado" class="form-select rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white shadow-sm focus-ring focus-ring-primary">
                            <option value="" class="text-dark">Todos</option>
                            <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }} class="text-dark">Pendiente</option>
                            <option value="completada" {{ request('estado')==='completada'?'selected':'' }} class="text-dark">Completada</option>
                            <option value="enviada" {{ request('estado')==='enviada'?'selected':'' }} class="text-dark">Enviada</option>
                            <option value="entregada" {{ request('estado')==='entregada'?'selected':'' }} class="text-dark">Entregada</option>
                            <option value="anulada" {{ request('estado')==='anulada'?'selected':'' }} class="text-dark">Anulada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-white-50 text-uppercase fw-bold">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}" class="form-control rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white shadow-sm focus-ring focus-ring-primary">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-white-50 text-uppercase fw-bold">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control rounded-pill bg-secondary bg-opacity-10 border-light border-opacity-10 text-white shadow-sm focus-ring focus-ring-primary">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm"><i class="bi bi-filter me-2"></i>Filtrar</button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-light w-auto rounded-circle shadow-sm d-flex align-items-center justify-content-center border-opacity-10" style="width: 38px; height: 38px;" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID</th>
                            <th class="py-3 border-0">Cliente</th>
                            <th class="py-3 border-0">Fecha</th>
                            <th class="py-3 border-0">Total</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="text-end pe-4 py-3 border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($ventas as $v)
                        <tr class="hover-bg-white-10 transition-base">
                            <td class="ps-4 fw-bold text-white-50 border-bottom border-light border-opacity-10">#{{ $v->id_venta }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-white bg-opacity-10 rounded-circle text-white me-3 d-flex align-items-center justify-content-center border border-light border-opacity-10" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $v->cliente->nombre ?? 'Cliente General' }}</div>
                                        <div class="small text-white-50">{{ $v->cliente->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap border-bottom border-light border-opacity-10">
                                <div class="small text-white fw-medium"><i class="bi bi-calendar3 me-1 text-primary"></i>{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y') }}</div>
                                <div class="small text-white-50"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($v->fecha)->format('H:i') }}</div>
                            </td>
                            <td class="fw-bold text-success border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($v->total, 2) }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                @php
                                    $statusClasses = [
                                        'pendiente' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                        'completada' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'enviada' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                        'entregada' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                        'anulada' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'
                                    ];
                                    $statusClass = $statusClasses[$v->estado] ?? 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 text-uppercase badge-status">
                                    {{ ucfirst($v->estado) }}
                                </span>
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border border-light border-opacity-10">
                                    <a href="{{ route('ventas.show', $v) }}" class="btn btn-sm btn-outline-light text-primary border-0 hover-scale" data-bs-toggle="tooltip" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <!-- Using target=_blank for ticket and invoice to open in new tab -->
                                    <a href="{{ route('ventas.ticket', $v) }}?print=1" target="_blank" class="btn btn-sm btn-outline-light text-white border-0 hover-scale" data-bs-toggle="tooltip" title="Imprimir Ticket">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <a href="{{ route('ventas.factura', $v) }}" target="_blank" class="btn btn-sm btn-outline-light text-danger border-0 hover-scale" data-bs-toggle="tooltip" title="PDF Factura">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 border-0">
                                <div class="text-white-50">
                                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-50"></i>
                                    No se encontraron ventas registradas.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($ventas, 'links'))
                <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3">
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
