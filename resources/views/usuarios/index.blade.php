@extends('layouts.app')
@section('title','Usuarios')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Usuarios</h4>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo</a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $u)
        <tr>
            <td>{{ $u->id_usuario }}</td>
            <td>{{ $u->nombre }} {{ $u->apellido }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->rol }}</td>
            <td>{{ $u->estado }}</td>
            <td class="text-end">
                <a href="{{ route('usuarios.edit',$u) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('usuarios.destroy',$u) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
 </table>
 {{ $usuarios->links() }}
@endsection
