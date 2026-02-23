@extends('layouts.app')
@section('title','Proveedor #'.$proveedor->id_proveedor)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Proveedor #{{ $proveedor->id_proveedor }}</h4>
    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <p><strong>Empresa:</strong> {{ $proveedor->nombre_empresa }}</p>
        <p><strong>NIT:</strong> {{ $proveedor->nit }}</p>
        <p><strong>Contacto:</strong> {{ $proveedor->contacto }}</p>
        <p><strong>Teléfono:</strong> {{ $proveedor->telefono }}</p>
        <p><strong>Dirección:</strong> {{ $proveedor->direccion }}</p>
        <p><strong>Email:</strong> {{ $proveedor->email }}</p>
        <p><strong>Estado:</strong> {{ $proveedor->estado }}</p>
    </div>
</div>
@endsection
