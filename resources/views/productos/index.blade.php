@extends('layouts.app')
@section('title','Productos')
@section('content')
<div class="container-fluid py-4">
    <div class="glass-card overflow-hidden">
        <div class="card-header bg-transparent py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center border-bottom border-light border-opacity-10 gap-3">
            <h4 class="mb-0 fw-bold text-white"><i class="bi bi-box-seam me-2 text-primary"></i>Productos</h4>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="{{ route('productos.papelera') }}" class="btn btn-outline-light btn-sm rounded-pill hover-scale flex-fill text-center"><i class="bi bi-trash"></i> Papelera</a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm rounded-pill hover-scale flex-fill text-center"><i class="bi bi-plus-lg"></i> Nuevo</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom border-light border-opacity-10 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <form class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto" method="GET" action="{{ route('productos.index') }}">
                    @if(request('filtro'))
                        <input type="hidden" name="filtro" value="{{ request('filtro') }}">
                    @endif
                    <div class="input-group w-100 w-md-auto">
                        <span class="input-group-text bg-secondary bg-opacity-10 border-0 text-white rounded-start-pill"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-secondary bg-opacity-10 text-white placeholder-light" placeholder="Buscar por nombre o código...">
                        <button class="btn btn-primary rounded-end-pill shadow-sm px-4 hover-scale" type="submit">Buscar</button>
                    </div>
                </form>
                
                @if(request('filtro') === 'stock_bajo')
                    <div class="alert alert-warning mb-0 py-2 px-3 rounded-pill d-flex align-items-center shadow-sm border-0 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span class="fw-bold me-2">Filtro: Stock Bajo</span>
                        <a href="{{ route('productos.index') }}" class="btn-close btn-close-white ms-2" aria-label="Close" style="filter: none; opacity: 1; color: inherit; font-size: 0.8rem;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-white">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-bottom border-light border-opacity-10 rounded-start-pill">Imagen</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">ID</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Nombre</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Categoría</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Proveedor</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Stock</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Precio</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Estado</th>
                            <th class="text-end pe-4 py-3 border-bottom border-light border-opacity-10 rounded-end-pill">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($productos as $p)
                        <tr class="hover-bg-white-10 transition-all">
                            <td class="ps-4 w-80-px border-bottom border-light border-opacity-10">
                                @if($p->imagen_url)
                                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" class="rounded-3 shadow-sm img-thumb-48 object-fit-cover">
                                @else
                                    <div class="bg-secondary bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white-50 small img-thumb-48 shadow-sm">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-medium text-white-50 border-bottom border-light border-opacity-10">#{{ $p->id_producto }}</td>
                            <td class="fw-bold text-white border-bottom border-light border-opacity-10">{{ $p->nombre }}</td>
                            <td class="border-bottom border-light border-opacity-10"><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">{{ $p->categoria->nombre ?? '-' }}</span></td>
                            <td class="small text-white-50 border-bottom border-light border-opacity-10">{{ $p->proveedor->nombre_empresa ?? '-' }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold {{ $p->stock <= $p->stock_minimo ? 'text-danger' : 'text-success' }}">{{ $p->stock }}</span>
                                    @if($p->stock_minimo !== null && $p->stock <= $p->stock_minimo)
                                        <span class="badge bg-warning text-dark ms-2 rounded-pill shadow-sm"><i class="bi bi-exclamation-triangle-fill me-1"></i>Bajo</span>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-bold text-success border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($p->precio_venta,2) }}</td>
                            <td class="border-bottom border-light border-opacity-10">
                                @if($p->estado == 'activo')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 border border-success border-opacity-25">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 border border-secondary border-opacity-25">{{ $p->estado }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                    <a href="{{ route('productos.etiqueta', $p->id_producto) }}" class="btn btn-sm btn-outline-light border-0 hover-scale" title="Imprimir Etiqueta" target="_blank"><i class="bi bi-upc-scan"></i></a>
                                    <a href="{{ route('productos.edit',$p) }}" class="btn btn-sm btn-outline-light border-0 hover-scale" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-light text-danger border-0 hover-scale" onclick="if(confirm('¿Mover a papelera?')) document.getElementById('delete-form-{{ $p->id_producto }}').submit()"><i class="bi bi-trash"></i></button>
                                </div>
                                <form id="delete-form-{{ $p->id_producto }}" action="{{ route('productos.destroy',$p) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @if($productos->hasPages())
        <div class="card-footer bg-transparent border-top-0 py-3">
            {{ $productos->links() }}
        </div>
    @endif
    </div>
</div>
@endsection
