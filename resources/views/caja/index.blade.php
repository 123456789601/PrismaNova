@extends('layouts.app')
@section('title','Caja')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Caja</h4>
    <form action="{{ route('caja.abrir') }}" method="POST">
        @csrf
        <button class="btn btn-primary">Abrir nueva caja</button>
    </form>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Apertura</th>
            <th>Cierre</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($cajas as $c)
        <tr>
            <td>{{ $c->id_caja }}</td>
            <td>{{ $c->fecha_apertura }}</td>
            <td>{{ $c->fecha_cierre }}</td>
            <td>{{ $c->estado }}</td>
            <td class="text-end">
                <a href="{{ route('caja.show',$c) }}" class="btn btn-sm btn-secondary">Ver</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $cajas->links() }}
@endsection
