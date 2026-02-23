@extends('layouts.app')
@section('title','Cupones')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Cupones</h4>
    <form class="d-flex me-auto ms-3" method="GET" action="{{ route('cupones.index') }}" style="max-width:420px;width:100%">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Buscar por código...">
        <button class="btn btn-sm btn-outline-secondary">Buscar</button>
    </form>
    <a href="{{ route('cupones.create') }}" class="btn btn-primary">Nuevo</a>
 </div>
<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Vigencia</th>
            <th>Estado</th>
            <th>Usos</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($cupones as $c)
        <tr>
            <td>{{ $c->id_cupon }}</td>
            <td>{{ $c->codigo }}</td>
            <td>{{ $c->tipo }}</td>
            <td>{{ $c->valor }}</td>
            <td>{{ optional($c->fecha_inicio)->format('d/m/Y') }} - {{ optional($c->fecha_fin)->format('d/m/Y') }}</td>
            <td>{{ $c->estado }}</td>
            <td>{{ $c->usos }} @if($c->uso_maximo) / {{ $c->uso_maximo }} @endif</td>
            <td class="text-end">
                <a href="{{ route('cupones.edit',$c) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('cupones.destroy',$c) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cupón?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $cupones->links() }}
@endsection
