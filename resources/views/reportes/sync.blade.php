@extends('layouts.app')
@section('title','Sync Inventario')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Sync de Inventario</h4>
    <form method="POST" action="{{ route('reportes.sync.run') }}">
        @csrf
        <button class="btn btn-primary">Sincronizar ahora</button>
    </form>
 </div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>External ID</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Aplicado</th>
            <th>Creado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $l)
        <tr>
            <td>{{ $l->id }}</td>
            <td>{{ $l->external_id }}</td>
            <td>
                @php $p = $l->payload ?? []; @endphp
                {{ $p['id_producto'] ?? '-' }} {{ isset($p['codigo_barras']) ? '(' . $p['codigo_barras'] . ')' : '' }}
            </td>
            <td>{{ $p['cantidad'] ?? '-' }}</td>
            <td>{{ optional($l->applied_at)->format('d/m/Y H:i') }}</td>
            <td>{{ $l->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $logs->links() }}
@endsection
