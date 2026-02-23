@extends('layouts.app')
@section('title','Nueva Compra')
@section('content')
<h4 class="mb-3">Nueva Compra</h4>
<form method="POST" action="{{ route('compras.store') }}">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Proveedor</label>
            <select class="form-select" name="id_proveedor" required>
                <option value="">Seleccione</option>
                @foreach($proveedores as $p)
                    <option value="{{ $p->id_proveedor }}">{{ $p->nombre_empresa }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input type="datetime-local" class="form-control" name="fecha" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Impuesto</label>
            <input type="number" step="0.01" class="form-control" name="impuesto" value="0">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="items">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio compra</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select class="form-select" name="id_producto[]">
                            @foreach($productos as $prod)
                                <option value="{{ $prod->id_producto }}">{{ $prod->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control" name="cantidad[]" value="1" min="1"></td>
                    <td><input type="number" step="0.01" class="form-control" name="precio_compra[]" value="0"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" onclick="addRow()">Agregar ítem</button>
    </div>
    <div class="mt-3">
        <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
<script>
function addRow(){
    const tbody = document.querySelector('#items tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select class="form-select" name="id_producto[]">
                @foreach($productos as $prod)
                    <option value="{{ $prod->id_producto }}">{{ $prod->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" class="form-control" name="cantidad[]" value="1" min="1"></td>
        <td><input type="number" step="0.01" class="form-control" name="precio_compra[]" value="0"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
    `;
    tbody.appendChild(tr);
}
function removeRow(btn){
    const tr = btn.closest('tr');
    tr.parentNode.removeChild(tr);
}
</script>
@endsection
