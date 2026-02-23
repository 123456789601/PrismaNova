@extends('layouts.app')
@section('title','Productos')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Productos</h4>
    <form class="d-flex me-auto ms-3" method="GET" action="{{ route('productos.index') }}" style="max-width:420px;width:100%">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Buscar por nombre o código...">
        <button class="btn btn-sm btn-outline-secondary" type="submit">Buscar</button>
    </form>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">Nuevo</a>
 </div>
<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Imagen</th>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Proveedor</th>
            <th>Stock</th>
            <th>Precio Venta</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $p)
        <tr>
            <td style="width:60px">
                @if($p->imagen_url)
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" style="width:48px;height:48px;object-fit:cover" class="rounded">
                @else
                    <div class="text-muted small">—</div>
                @endif
            </td>
            <td>{{ $p->id_producto }}</td>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria->nombre ?? '-' }}</td>
            <td>{{ $p->proveedor->nombre_empresa ?? '-' }}</td>
            <td>
                {{ $p->stock }}
                @if($p->stock_minimo !== null && $p->stock <= $p->stock_minimo)
                    <span class="badge bg-warning text-dark ms-1">Bajo</span>
                @endif
            </td>
            <td>{{ number_format($p->precio_venta,2) }}</td>
            <td>{{ $p->estado }}</td>
            <td class="text-end">
                <a href="{{ route('productos.edit',$p) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('productos.destroy',$p) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $productos->links() }}
@endsection
