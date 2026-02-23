@extends('layouts.app')
@section('title','Caja #'.$caja->id_caja)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Caja #{{ $caja->id_caja }}</h4>
    @if($caja->estado==='abierta')
    <form action="{{ route('caja.cerrar',$caja) }}" method="POST">
        @csrf @method('PATCH')
        <button class="btn btn-danger">Cerrar caja</button>
    </form>
    @endif
 </div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p><strong>Apertura:</strong> {{ $caja->fecha_apertura }}</p>
                <p><strong>Estado:</strong> {{ $caja->estado }}</p>
                <p><strong>Monto inicial:</strong> {{ number_format($caja->monto_inicial,2) }}</p>
                <p><strong>Monto final:</strong> {{ number_format($caja->monto_final,2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        @if($caja->estado==='abierta')
        <form method="POST" action="{{ route('caja.movimiento.store',$caja) }}" class="mb-3">
            @csrf
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" name="tipo">
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto" required>
                </div>
                <div class="col-md-4">
                    <input name="descripcion" class="form-control" placeholder="Descripción">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Agregar</button>
                </div>
            </div>
        </form>
        @endif
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($caja->movimientos as $m)
                <tr>
                    <td>{{ $m->fecha }}</td>
                    <td>{{ $m->tipo }}</td>
                    <td>{{ number_format($m->monto,2) }}</td>
                    <td>{{ $m->descripcion }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
