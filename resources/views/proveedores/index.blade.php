@extends('layouts.app')
@section('title','Proveedores')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Proveedores</h4>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary">Nuevo</a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Empresa</th>
            <th>NIT</th>
            <th>Contacto</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($proveedores as $p)
        <tr>
            <td>{{ $p->id_proveedor }}</td>
            <td>{{ $p->nombre_empresa }}</td>
            <td>{{ $p->nit }}</td>
            <td>{{ $p->contacto }}</td>
            <td>{{ $p->estado }}</td>
            <td class="text-end">
                <a href="{{ route('proveedores.edit',$p) }}" class="btn btn-sm btn-secondary">Editar</a>
                <form action="{{ route('proveedores.destroy',$p) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $proveedores->links() }}
@endsection
