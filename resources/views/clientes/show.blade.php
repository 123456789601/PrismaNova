@extends('layouts.app')
@section('title','Cliente #'.$cliente->id_cliente)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Cliente #{{ $cliente->id_cliente }}</h4>
    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <p><strong>Nombre:</strong> {{ $cliente->nombre }} {{ $cliente->apellido }}</p>
        <p><strong>Documento:</strong> {{ $cliente->documento }}</p>
        <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
        <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
        <p><strong>Email:</strong> {{ $cliente->email }}</p>
        <p><strong>Estado:</strong> {{ $cliente->estado }}</p>
    </div>
</div>
@endsection
