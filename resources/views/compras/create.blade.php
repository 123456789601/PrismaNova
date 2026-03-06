@extends('layouts.app')
@section('title','Nueva Compra')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-cart-plus me-2 text-primary"></i>Registrar Compra</h5>
                    <a href="{{ route('compras.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 hover-scale">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('compras.store') }}" id="formCompra" novalidate>
                        @csrf
                        <div class="row g-3 mb-4 p-4 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                            <div class="col-md-4">
                                <label for="id_proveedor" class="form-label fw-bold small text-white-50">Proveedor <span class="text-danger">*</span></label>
                                <select class="form-select border-0 rounded-pill bg-secondary bg-opacity-10 text-white" name="id_proveedor" id="id_proveedor" required>
                                    <option value="" class="text-dark">Seleccione Proveedor...</option>
                                    @foreach($proveedores as $p)
                                        <option value="{{ $p->id_proveedor }}" class="text-dark" {{ old('id_proveedor') == $p->id_proveedor ? 'selected' : '' }}>{{ $p->nombre_empresa }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback ms-2">Seleccione un proveedor.</div>
                                @error('id_proveedor')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="fecha" class="form-label fw-bold small text-white-50">Fecha <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control border-0 rounded-pill bg-secondary bg-opacity-10 text-white" name="fecha" id="fecha" value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}" required>
                                <div class="invalid-feedback ms-2">Seleccione una fecha válida.</div>
                                @error('fecha')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="impuesto" class="form-label fw-bold small text-white-50">Impuesto</label>
                                <div class="input-group rounded-pill overflow-hidden">
                                    <span class="input-group-text border-0 ps-3 bg-secondary bg-opacity-10 text-white">{{ $configuracion['moneda'] ?? '$' }} </span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-secondary bg-opacity-10 text-white" name="impuesto" id="impuesto" value="{{ old('impuesto', '0.00') }}" min="0">
                                </div>
                                @error('impuesto')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="table-responsive mb-4 rounded-4 overflow-hidden border border-light border-opacity-10">
                            <table class="table table-hover align-middle mb-0 text-white" id="items">
                                <thead class="bg-primary bg-opacity-10 text-white">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Producto</th>
                                        <th class="py-3 border-0" style="width: 150px;">Cantidad</th>
                                        <th class="py-3 border-0" style="width: 200px;">Precio Compra</th>
                                        <th class="text-end pe-4 py-3 border-0" style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
                                        <td class="ps-4">
                                            <select class="form-select rounded-pill bg-secondary bg-opacity-10 text-white border-0" name="id_producto[]" required>
                                                <option value="" class="text-dark">Seleccione...</option>
                                                @foreach($productos as $prod)
                                                    <option value="{{ $prod->id_producto }}" class="text-dark">{{ $prod->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rounded-pill bg-secondary bg-opacity-10 text-white border-0" name="cantidad[]" value="1" min="1" required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text rounded-start-pill bg-secondary bg-opacity-10 text-white border-0">{{ $configuracion['moneda'] ?? '$' }} </span>
                                                <input type="number" step="0.01" class="form-control rounded-end-pill border-0 bg-secondary bg-opacity-10 text-white" name="precio_compra[]" value="0.00" min="0" required>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle hover-scale" style="width: 32px; height: 32px; padding: 0;" onclick="removeRow(this)">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 hover-scale" onclick="addRow()">
                                <i class="bi bi-plus-lg me-1"></i>Agregar ítem
                            </button>
                            <div class="h5 mb-0 text-white-50">
                                Total: <span class="text-primary fw-bold" id="total_compra">{{ $configuracion['moneda'] ?? '$' }} 0.00</span>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('compras.index') }}" class="btn btn-secondary rounded-pill px-4 me-2 hover-scale">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm hover-scale">
                                <i class="bi bi-check-lg me-1"></i>Guardar Compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="row-template">
    <tr class="hover-bg-white-10 border-bottom border-light border-opacity-10">
        <td class="ps-4">
            <select class="form-select rounded-pill bg-secondary bg-opacity-10 text-white border-0" name="id_producto[]" required>
                <option value="" class="text-dark">Seleccione...</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id_producto }}" class="text-dark">{{ $prod->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control rounded-pill bg-secondary bg-opacity-10 text-white border-0" name="cantidad[]" value="1" min="1" required>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text rounded-start-pill bg-secondary bg-opacity-10 text-white border-0">{{ $configuracion['moneda'] ?? '$' }} </span>
                <input type="number" step="0.01" class="form-control rounded-end-pill border-0 bg-secondary bg-opacity-10 text-white" name="precio_compra[]" value="0.00" min="0" required>
            </div>
        </td>
        <td class="text-end pe-4">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle hover-scale" style="width: 32px; height: 32px; padding: 0;" onclick="removeRow(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    </tr>
</template>

@section('scripts')
<script>
    function addRow(){
        const tbody = document.querySelector('#items tbody');
        const template = document.getElementById('row-template');
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
        calculateTotal();
        attachEventListeners();
    }

    function removeRow(btn){
        const tr = btn.closest('tr');
        if(document.querySelectorAll('#items tbody tr').length > 1) {
            tr.remove();
            calculateTotal();
        } else {
            alert('Debe haber al menos un ítem en la compra.');
        }
    }

    function calculateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#items tbody tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[name="cantidad[]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name="precio_compra[]"]').value) || 0;
            total += qty * price;
        });
        
        const impuesto = parseFloat(document.getElementById('impuesto').value) || 0;
        total += impuesto;
        
        document.getElementById('total_compra').innerText = `{{ $configuracion['moneda'] ?? '$' }} ${total.toFixed(2)}`;
    }

    function attachEventListeners() {
        document.querySelectorAll('input[name="cantidad[]"], input[name="precio_compra[]"]').forEach(input => {
            input.removeEventListener('input', calculateTotal);
            input.addEventListener('input', calculateTotal);
        });
    }

    document.getElementById('impuesto').addEventListener('input', calculateTotal);

    // Bootstrap validation
    (function () {
        'use strict'
        const form = document.getElementById('formCompra');
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })();

    // Initial total calculation
    calculateTotal();
    attachEventListeners();
</script>
@endsection
