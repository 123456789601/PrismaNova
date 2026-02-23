@extends('layouts.app')
@section('title','Ventas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Ventas</h4>
    <form class="d-flex me-auto ms-3" method="GET" action="{{ route('ventas.index') }}" style="gap:.5rem;flex-wrap:wrap">
        <select name="estado" class="form-select form-select-sm" style="width:150px">
            <option value="">Estado</option>
            <option value="completada" {{ request('estado')==='completada'?'selected':'' }}>Completada</option>
            <option value="anulada" {{ request('estado')==='anulada'?'selected':'' }}>Anulada</option>
        </select>
        <input type="date" name="desde" value="{{ request('desde') }}" class="form-control form-control-sm" style="width:150px">
        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control form-control-sm" style="width:150px">
        <button class="btn btn-sm btn-outline-secondary">Filtrar</button>
        <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-dark">Limpiar</a>
    </form>
    <a href="{{ route('ventas.create') }}" class="btn btn-primary">Nueva</a>
</div>
<div class="table-responsive">
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventas as $v)
        <tr>
            <td>{{ $v->id_venta }}</td>
            <td>{{ $v->cliente->nombre ?? '-' }}</td>
            <td>{{ $v->fecha }}</td>
            <td>{{ number_format($v->total,2) }}</td>
            <td>{{ $v->estado }}</td>
            <td class="text-end">
                @if($v->estado!=='anulada')
                <form action="{{ route('ventas.anular',$v) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-warning" onclick="return confirm('¿Anular venta?')">Anular</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $ventas->links() }}
@endsection
