@php($cliente = $venta->cliente)
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura A #{{ $venta->id_venta }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        @media print {.no-print{display:none!important}}
        .factura-header{border-bottom:2px solid #000;padding-bottom:.5rem;margin-bottom:1rem}
        .totales td{font-weight:600}
        .table-sm td,.table-sm th{padding:.35rem}
    </style>
    <script>
        function imprimir(){ window.print(); }
    </script>
    </head>
<body class="bg-white">
<div class="container my-3">
    <div class="d-flex justify-content-between align-items-center factura-header">
        <div>
            <h4 class="mb-0">Factura A</h4>
            <small>N° {{ $venta->id_venta }}</small>
        </div>
        <div class="text-end">
            <div class="fw-bold">PRISMANOVA</div>
            <div>Fecha: {{ $venta->fecha->format('d/m/Y H:i') }}</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <div class="border rounded p-2">
                <div class="fw-semibold mb-1">Cliente</div>
                <div>{{ $cliente->nombre ?? '-' }} {{ $cliente->apellido ?? '' }}</div>
                <div>Doc: {{ $cliente->documento ?? '-' }}</div>
                <div>Dirección: {{ $cliente->direccion ?? '-' }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2">
                <div class="fw-semibold mb-1">Detalles</div>
                <div>Método de pago: {{ $venta->metodoPago->nombre ?? $venta->metodo_pago ?? '-' }}</div>
                <div>Estado: {{ $venta->estado }}</div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $d)
                <tr>
                    <td>{{ $d->producto->nombre ?? 'N/D' }}</td>
                    <td class="text-end">{{ $d->cantidad }}</td>
                    <td class="text-end">{{ number_format($d->precio_unitario,2) }}</td>
                    <td class="text-end">{{ number_format($d->subtotal,2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end">Subtotal</td>
                    <td class="text-end">{{ number_format($venta->subtotal,2) }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end">Descuento</td>
                    <td class="text-end">{{ number_format($venta->descuento,2) }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end">Impuesto</td>
                    <td class="text-end">{{ number_format($venta->impuesto,2) }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end fw-bold">Total</td>
                    <td class="text-end fw-bold">{{ number_format($venta->total,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex gap-2 no-print">
        <a href="{{ route('ventas.show',$venta) }}" class="btn btn-secondary">Volver</a>
        <button class="btn btn-primary" onclick="imprimir()">Imprimir / PDF</button>
    </div>
    <div class="text-muted small mt-3">
        Este comprobante es una representación impresa. Para PDF, usa la función de impresión del navegador y selecciona “Guardar como PDF”.
    </div>
 </div>
</body>
</html>
