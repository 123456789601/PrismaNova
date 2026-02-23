@extends('layouts.app')
@section('title','Categorías')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Categorías</h4>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">Nueva</a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($categorias as $c)
        <tr>
            <td>{{ $c->id_categoria }}</td>
            <td>{{ $c->nombre }}</td>
            <td>{{ $c->estado }}</td>
            <td class="text-end">
                <a href="{{ route('categorias.edit',$c) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('categorias.destroy',$c) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $categorias->links() }}
@endsection
