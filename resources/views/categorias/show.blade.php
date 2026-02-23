@extends('layouts.app')
@section('title','Categoría #'.$categoria->id_categoria)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Categoría #{{ $categoria->id_categoria }}</h4>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <p><strong>Nombre:</strong> {{ $categoria->nombre }}</p>
        <p><strong>Descripción:</strong> {{ $categoria->descripcion }}</p>
        <p><strong>Estado:</strong> {{ $categoria->estado }}</p>
    </div>
</div>
@endsection
