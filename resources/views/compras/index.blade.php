@extends('layouts.app')
@section('title','Compras')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Compras</h4>
    <a href="{{ route('compras.create') }}" class="btn btn-primary">Nueva</a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Proveedor</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($compras as $c)
        <tr>
            <td>{{ $c->id_compra }}</td>
            <td>{{ $c->proveedor->nombre_empresa ?? '-' }}</td>
            <td>{{ $c->fecha }}</td>
            <td>{{ number_format($c->total,2) }}</td>
            <td>{{ $c->estado }}</td>
            <td class="text-end">
                @if($c->estado!=='anulada')
                <form action="{{ route('compras.anular',$c) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-warning" onclick="return confirm('¿Anular compra?')">Anular</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $compras->links() }}
@endsection
