@extends('layouts.app')
@section('title','Usuario #'.$usuario->id_usuario)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Usuario #{{ $usuario->id_usuario }}</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Volver</a>
 </div>
<div class="card">
    <div class="card-body">
        <p><strong>Nombre:</strong> {{ $usuario->nombre }} {{ $usuario->apellido }}</p>
        <p><strong>Documento:</strong> {{ $usuario->documento }}</p>
        <p><strong>Email:</strong> {{ $usuario->email }}</p>
        <p><strong>Rol:</strong> {{ $usuario->rol }}</p>
        <p><strong>Estado:</strong> {{ $usuario->estado }}</p>
    </div>
</div>
@endsection
