@extends('layouts.app')
@section('title','Mis Compras')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Mis Compras</h4>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
</div>
@if(!$cliente)
    <div class="alert alert-warning">No se encontró un registro de cliente asociado a tu usuario.</div>
@endif
<div class="table-responsive">
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($ventas as $v)
        <tr>
            <td>{{ $v->id_venta }}</td>
            <td>{{ $v->fecha }}</td>
            <td>{{ number_format($v->total,2) }}</td>
            <td>{{ $v->estado }}</td>
            <td class="text-end">
                <a href="{{ route('mis-compras.show',$v) }}" class="btn btn-sm btn-primary">Ver</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">Sin compras registradas.</td></tr>
        @endforelse
    </tbody>
 </table>
</div>
 @if(method_exists($ventas,'links'))
    {{ $ventas->links() }}
 @endif
@endsection
