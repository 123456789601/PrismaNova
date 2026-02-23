@extends('layouts.app')
@section('title','Clientes')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Clientes</h4>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">Nuevo</a>
    </div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $c)
        <tr>
            <td>{{ $c->id_cliente }}</td>
            <td>{{ $c->nombre }} {{ $c->apellido }}</td>
            <td>{{ $c->documento }}</td>
            <td>{{ $c->telefono }}</td>
            <td>{{ $c->estado }}</td>
            <td class="text-end">
                <a href="{{ route('clientes.edit',$c) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('clientes.destroy',$c) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $clientes->links() }}
@endsection
