@extends('layouts.app')
@section('title','Nueva Venta')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="glass-card">
                <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-cart-plus me-2"></i>Registrar Venta</h5>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm w-100 w-md-auto text-center">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
                        @csrf
                        {{-- Encabezado Venta --}}
                        <div class="row g-3 mb-4 p-3 p-md-4 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-white-50">Cliente <span class="text-danger">*</span></label>
                                <select class="form-select border-0 rounded-pill bg-secondary bg-opacity-25 text-white focus-ring-primary" name="id_cliente" required>
                                    <option value="" class="text-dark">Seleccione Cliente...</option>
                                    @foreach($clientes as $c)
                                        <option value="{{ $c->id_cliente }}" class="text-dark">{{ $c->nombre }} {{ $c->apellido }} ({{ $c->documento }})</option>
                                    @endforeach
                                </select>
                                @error('id_cliente')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-white-50">Fecha <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control border-0 rounded-pill bg-secondary bg-opacity-25 text-white focus-ring-primary" name="fecha" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
                                @error('fecha')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-white-50">Impuesto ({{ $configuracion['moneda'] ?? '$' }} )</label>
                                <input type="number" step="0.01" class="form-control border-0 rounded-pill bg-secondary bg-opacity-25 text-white focus-ring-primary" name="impuesto" id="impuesto" value="0" oninput="calcularTotales()">
                                @error('impuesto')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-white-50">Método de Pago</label>
                                <select class="form-select border-0 rounded-pill bg-secondary bg-opacity-25 text-white focus-ring-primary" name="metodo_pago_id" id="metodo_pago_id" required>
                                    <option value="" class="text-dark">Seleccione...</option>
                                    @isset($metodos)
                                        @foreach($metodos as $m)
                                            <option value="{{ $m->id_metodo_pago }}" class="text-dark">{{ $m->nombre }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <input type="hidden" name="metodo_pago" id="metodo_pago_nombre" value="">
                                @error('metodo_pago_id')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 d-none" id="pagoDetallesWrap">
                                <div class="p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 shadow-sm">
                                    <div class="fw-bold text-white mb-3"><i class="bi bi-wallet2 me-2 text-primary"></i>Datos de pago</div>

                                    <div id="pagoEfectivoFields" class="d-none">
                                        <label class="form-label fw-bold small text-white-50">Monto Recibido ({{ $configuracion['moneda'] ?? '$' }} )</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-secondary bg-opacity-25 text-white-50">{{ $configuracion['moneda'] ?? '$' }} </span>
                                            <input type="number" step="0.01" class="form-control border-0 bg-secondary bg-opacity-25 text-white" name="monto_recibido" id="monto_recibido" placeholder="0.00">
                                        </div>
                                        @error('monto_recibido')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    <div id="pagoTransferenciaFields" class="d-none">
                                        <div class="mb-3">
                                            <div class="text-white-50 small">Banco destino: <span class="text-white">{{ $configuracion['banco_nombre'] ?? 'Bancolombia' }}</span></div>
                                            <div class="text-white-50 small">Tipo: <span class="text-white">{{ $configuracion['banco_tipo_cuenta'] ?? 'Ahorros' }}</span></div>
                                            <div class="text-white-50 small">Cuenta: <span class="text-white">{{ $configuracion['banco_numero_cuenta'] ?? '00000000000' }}</span></div>
                                            <div class="text-white-50 small">Titular: <span class="text-white">{{ $configuracion['banco_titular'] ?? 'PrismaNova' }}</span></div>
                                            @if(!empty($configuracion['banco_nit']))
                                                <div class="text-white-50 small">NIT: <span class="text-white">{{ $configuracion['banco_nit'] }}</span></div>
                                            @endif
                                        </div>
                                        <label class="form-label fw-bold small text-white-50">Referencia / Comprobante</label>
                                        <input type="text" class="form-control border-0 bg-secondary bg-opacity-25 text-white" name="referencia_pago" id="referencia_pago" maxlength="50" placeholder="Ej: 00123456">
                                        @error('referencia_pago')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    <div id="pagoTarjetaFields" class="d-none">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-white-50">Referencia / Nro. Operación</label>
                                            <input type="text" class="form-control border-0 bg-secondary bg-opacity-25 text-white" name="referencia_pago" id="referencia_pago_tarjeta" maxlength="50" placeholder="Ej: 00123456">
                                            @error('referencia_pago')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label fw-bold small text-white-50">Últimos 4 dígitos</label>
                                            <input type="text" class="form-control border-0 bg-secondary bg-opacity-25 text-white" name="ultimos_digitos" id="ultimos_digitos" maxlength="4" placeholder="Ej: 4242">
                                            @error('ultimos_digitos')<div class="text-danger small ms-2 mt-1">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Detalle de Productos --}}
                        <div class="table-responsive mb-4 rounded-4 overflow-hidden shadow-sm border border-light border-opacity-10">
                            <table class="table align-middle mb-0 text-white" id="tablaDetalles">
                                <thead class="bg-primary bg-opacity-10 text-white">
                                    <tr>
                                        <th class="ps-4 py-3 border-0" style="width: 40%;">Producto</th>
                                        <th class="py-3 border-0 text-center" style="width: 15%;">Stock</th>
                                        <th class="py-3 border-0" style="width: 15%;">Cantidad</th>
                                        <th class="py-3 border-0" style="width: 20%;">Precio Unit.</th>
                                        <th class="py-3 border-0 text-end pe-4" style="width: 10%;">Subtotal</th>
                                        <th class="py-3 border-0" style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-transparent">
                                    {{-- Las filas se agregan dinámicamente con JS --}}
                                </tbody>
                            </table>
                            <div class="p-3 bg-secondary bg-opacity-10 border-top border-light border-opacity-10">
                                <button type="button" class="btn btn-outline-light btn-sm rounded-pill shadow-sm fw-bold hover-scale" onclick="agregarFila()">
                                    <i class="bi bi-plus-circle me-2"></i>Agregar Producto
                                </button>
                            </div>
                        </div>

                        {{-- Totales y Descuentos --}}
                        <div class="row justify-content-end g-3 mt-2">
                            <div class="col-md-5 col-lg-4">
                                <div class="glass-card rounded-4 p-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-white-50">Subtotal:</span>
                                        <span class="fw-bold fs-5 text-white">{{ $configuracion['moneda'] ?? '$' }} <span id="lblSubtotal">0.00</span></span>
                                    </div>
                                    
                                    <div class="mb-3 border-bottom border-light border-opacity-10 pb-3">
                                        <label class="form-label fw-bold small text-white-50">Descuento Global ({{ $configuracion['moneda'] ?? '$' }} )</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-secondary bg-opacity-25 text-white-50">{{ $configuracion['moneda'] ?? '$' }} </span>
                                            <input type="number" step="0.01" min="0" class="form-control border-0 bg-secondary bg-opacity-25 text-white" name="descuento" id="descuento" value="0" oninput="calcularTotales()">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-white-50">Cupón</label>
                                        <input type="text" class="form-control form-control-sm rounded-pill bg-secondary bg-opacity-25 text-white border-0" name="cupon" placeholder="Código">
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-3 border-top border-light border-opacity-10">
                                        <span class="fw-bold text-primary fs-4">TOTAL:</span>
                                        <span class="fw-bold text-primary fs-3">{{ $configuracion['moneda'] ?? '$' }} <span id="lblTotal">0.00</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="{{ route('ventas.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg transform-hover" id="btnGuardar">
                                <i class="bi bi-check-lg me-2"></i>Finalizar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Template para filas de productos (oculto) --}}
<template id="filaTemplate">
    <tr>
        <td class="ps-4 border-0">
            <select class="form-select border-0 bg-secondary bg-opacity-25 rounded-pill text-white focus-ring-primary producto-select" name="id_producto[]" onchange="productoSeleccionado(this)" required>
                <option value="" data-precio="0" data-stock="0" class="text-dark">Seleccione producto...</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id_producto }}" data-precio="{{ $prod->precio_venta }}" data-stock="{{ $prod->stock }}" class="text-dark">
                        {{ $prod->nombre }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="text-center border-0">
            <span class="badge bg-secondary stock-badge">0</span>
        </td>
        <td class="border-0">
            <input type="number" class="form-control border-0 bg-secondary bg-opacity-25 rounded-pill text-white focus-ring-primary cantidad-input" name="cantidad[]" value="1" min="1" oninput="calcularFila(this)" required>
        </td>
        <td class="border-0">
            <div class="input-group input-group-sm">
                <span class="input-group-text border-0 bg-secondary bg-opacity-25 rounded-start-pill text-white-50">{{ $configuracion['moneda'] ?? '$' }} </span>
                <input type="number" step="0.01" class="form-control border-0 bg-secondary bg-opacity-25 rounded-end-pill text-white focus-ring-primary precio-input" name="precio_unitario[]" value="0" min="0" oninput="calcularFila(this)" required>
            </div>
        </td>
        <td class="text-end pe-4 border-0 fw-bold subtotal-text text-white">{{ $configuracion['moneda'] ?? '$' }} 0.00</td>
        <td class="text-center border-0">
            <button type="button" class="btn btn-sm btn-outline-light text-danger border-0 rounded-circle shadow-sm hover-scale" onclick="eliminarFila(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar primera fila al cargar
        agregarFila();

        const mpSelect = document.getElementById('metodo_pago_id');
        if (mpSelect) {
            mpSelect.addEventListener('change', syncPagoFields);
            syncPagoFields();
        }
    });

    function syncPagoFields() {
        const select = document.getElementById('metodo_pago_id');
        const wrap = document.getElementById('pagoDetallesWrap');
        const efectivo = document.getElementById('pagoEfectivoFields');
        const transferencia = document.getElementById('pagoTransferenciaFields');
        const tarjeta = document.getElementById('pagoTarjetaFields');

        if (!select || !wrap || !efectivo || !transferencia || !tarjeta) return;

        const opt = select.options[select.selectedIndex];
        const nombre = (opt ? opt.textContent : '').trim();
        document.getElementById('metodo_pago_nombre').value = nombre;

        wrap.classList.add('d-none');
        efectivo.classList.add('d-none');
        transferencia.classList.add('d-none');
        tarjeta.classList.add('d-none');

        const disableAll = (enabled) => {
            ['monto_recibido','referencia_pago','referencia_pago_tarjeta','ultimos_digitos'].forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.disabled = !enabled;
            });
        };
        disableAll(false);

        const lower = nombre.toLowerCase();
        if (!lower || lower === 'seleccione...') {
            return;
        }

        wrap.classList.remove('d-none');

        if (lower === 'efectivo') {
            efectivo.classList.remove('d-none');
            const el = document.getElementById('monto_recibido');
            if (el) el.disabled = false;
            return;
        }

        if (lower === 'transferencia') {
            transferencia.classList.remove('d-none');
            const el = document.getElementById('referencia_pago');
            if (el) el.disabled = false;
            return;
        }

        tarjeta.classList.remove('d-none');
        const ref = document.getElementById('referencia_pago_tarjeta');
        const ult = document.getElementById('ultimos_digitos');
        if (ref) ref.disabled = false;
        if (ult) ult.disabled = false;
    }

    function agregarFila() {
        const template = document.getElementById('filaTemplate');
        const clone = template.content.cloneNode(true);
        document.querySelector('#tablaDetalles tbody').appendChild(clone);
    }

    function eliminarFila(btn) {
        const tbody = document.querySelector('#tablaDetalles tbody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
            calcularTotales();
        } else {
            alert('Debe haber al menos un producto en la venta.');
        }
    }

    function productoSeleccionado(select) {
        const option = select.options[select.selectedIndex];
        const precio = option.getAttribute('data-precio') || 0;
        const stock = option.getAttribute('data-stock') || 0;
        
        const row = select.closest('tr');
        const precioInput = row.querySelector('.precio-input');
        const stockBadge = row.querySelector('.stock-badge');
        const cantidadInput = row.querySelector('.cantidad-input');

        precioInput.value = parseFloat(precio).toFixed(2);
        stockBadge.textContent = stock;
        
        // Validar stock visualmente
        if(parseInt(stock) === 0) {
            stockBadge.className = 'badge bg-danger stock-badge';
        } else if(parseInt(stock) < 5) {
            stockBadge.className = 'badge bg-warning text-dark stock-badge';
        } else {
            stockBadge.className = 'badge bg-success stock-badge';
        }
        
        // Reset cantidad a 1
        cantidadInput.value = 1;
        cantidadInput.max = stock; // Opcional: restringir input max

        calcularFila(select);
    }

    function calcularFila(element) {
        const row = element.closest('tr');
        const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
        const subtotal = cantidad * precio;

        row.querySelector('.subtotal-text').textContent = '{{ $configuracion['moneda'] ?? '$' }} ' + subtotal.toFixed(2);
        calcularTotales();
    }

    function calcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('#tablaDetalles tbody tr').forEach(row => {
            const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
            subtotal += cantidad * precio;
        });

        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const impuesto = parseFloat(document.getElementById('impuesto').value) || 0;
        
        // El impuesto suma, el descuento resta
        // Asumiendo que el impuesto es un monto fijo según el input ({{ $configuracion['moneda'] ?? '$' }} ), no un porcentaje calculado aquí
        // El label dice "Impuesto ({{ $configuracion['moneda'] ?? '$' }} )" en mi código nuevo, aunque antes decía % en el viejo.
        // Voy a mantenerlo como monto fijo para simplificar, o si es %, debería calcularse sobre el subtotal.
        // El input actual dice "Impuesto ({{ $configuracion['moneda'] ?? '$' }} )", así que lo trato como monto.
        
        const total = Math.max(0, subtotal - descuento + impuesto);

        document.getElementById('lblSubtotal').textContent = subtotal.toFixed(2);
        document.getElementById('lblTotal').textContent = total.toFixed(2);
    }

    // Validación antes de enviar
    document.getElementById('formVenta').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#tablaDetalles tbody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Agregue al menos un producto.');
            return;
        }
        
        let stockError = false;
        rows.forEach(row => {
            const select = row.querySelector('.producto-select');
            const option = select.options[select.selectedIndex];
            const stock = parseInt(option.getAttribute('data-stock') || 0);
            const cantidad = parseInt(row.querySelector('.cantidad-input').value || 0);
            
            if (cantidad > stock) {
                stockError = true;
                row.querySelector('.cantidad-input').classList.add('is-invalid');
            } else {
                row.querySelector('.cantidad-input').classList.remove('is-invalid');
            }
        });

        if (stockError) {
            e.preventDefault();
            alert('Uno o más productos superan el stock disponible.');
        }
    });
</script>
@endsection
