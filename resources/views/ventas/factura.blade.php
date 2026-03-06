<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Electrónica #{{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            background-color: white;
            position: relative;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .company-details {
            text-align: right;
        }
        .company-details h2 {
            margin: 0;
            color: #333;
        }
        .invoice-title {
            color: #555;
            font-size: 2em;
            margin-bottom: 0;
        }
        .invoice-details-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        table td {
            padding: 5px;
            vertical-align: top;
        }
        table tr td:nth-child(2), table tr td:nth-child(3), table tr td:nth-child(4) {
            text-align: right;
        }
        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        table tr.item td {
            border-bottom: 1px solid #eee;
        }
        table tr.total td:nth-child(3) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            font-size: 12px;
        }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black; }
        
        @media print {
            body { background-color: white; }
            .invoice-box { box-shadow: none; border: none; }
            .no-print { display: none; }
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center;">
    <button onclick="window.print()" class="btn-print">Imprimir / Guardar PDF</button>
</div>

<div class="invoice-box">
    <div class="invoice-header">
        <div>
            <h1 style="color: #007bff; margin: 0;">PRISMANOVA</h1>
            <p style="margin: 5px 0;">NIT: 900.123.456-7</p>
            <p style="margin: 5px 0;">Régimen Común</p>
        </div>
        <div class="company-details">
            <h2>FACTURA DE VENTA</h2>
            <h3 style="color: #d9534f; margin: 5px 0;">No. {{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}</h3>
            <p>Fecha de Expedición: {{ $venta->fecha->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="invoice-details-box">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%;">
                    <strong>Datos del Cliente:</strong><br>
                    {{ $venta->cliente->nombre ?? 'Consumidor Final' }}<br>
                    {{ $venta->cliente->documento ?? '222222222222' }}<br>
                    {{ $venta->cliente->direccion ?? 'Ciudad' }}<br>
                    {{ $venta->cliente->email ?? '' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Resolución DIAN:</strong><br>
                    No. 18760000001 de 2024-01-01<br>
                    Rango: 1 a 100000<br>
                    Estado: <span class="status-badge {{ $venta->estado == 'completada' ? 'bg-success' : ($venta->estado == 'anulada' ? 'bg-danger' : 'bg-warning') }}">
                        {{ strtoupper($venta->estado) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td>Descripción</td>
            <td style="width: 15%;">Cant.</td>
            <td style="width: 15%;">Precio Unit.</td>
            <td style="width: 15%;">Total</td>
        </tr>

        @foreach($venta->detalles as $detalle)
        <tr class="item">
            <td>
                {{ $detalle->producto->nombre ?? 'Producto Eliminado' }}
                <br><small style="color: #777;">Código: {{ $detalle->producto->codigo_barras ?? 'N/A' }}</small>
            </td>
            <td>{{ $detalle->cantidad }}</td>
            <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
            <td>${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach

        <tr class="total">
            <td colspan="2"></td>
            <td>Subtotal:</td>
            <td>${{ number_format($venta->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td colspan="2"></td>
            <td>IVA (19%):</td>
            <td>${{ number_format($venta->impuesto, 0, ',', '.') }}</td>
        </tr>
        @if($venta->descuento > 0)
        <tr class="total" style="color: #28a745;">
            <td colspan="2"></td>
            <td>Descuento:</td>
            <td>-${{ number_format($venta->descuento, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total" style="font-size: 1.2em; color: #000;">
            <td colspan="2"></td>
            <td>Total a Pagar:</td>
            <td>${{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
        
        @if($venta->monto_recibido > 0)
        <tr>
            <td colspan="2"></td>
            <td style="color: #555;">Efectivo Recibido:</td>
            <td style="color: #555;">${{ number_format($venta->monto_recibido, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="color: #555;">Cambio:</td>
            <td style="color: #555;">${{ number_format($venta->cambio, 0, ',', '.') }}</td>
        </tr>
        @endif
        
        @if($venta->referencia_pago)
        <tr>
            <td colspan="2"></td>
            <td style="color: #555;">Tarjeta (**** {{ $venta->ultimos_digitos }}):</td>
            <td style="color: #555;">Ref: {{ $venta->referencia_pago }}</td>
        </tr>
        @endif
    </table>

    <div class="row" style="margin-top: 30px; display: flex;">
        <div class="col-6" style="width: 70%;">
            <p><strong>Observaciones:</strong></p>
            <p>Esta factura de venta se asimila en todos sus efectos a una letra de cambio según el Art. 774 del Código de Comercio.</p>
            <p>Gracias por su compra.</p>
        </div>
        <div class="col-6 qr-code" style="width: 30%;">
            <!-- QR Code generating URL with invoice data -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=Factura:{{ $venta->id_venta }}|Fecha:{{ $venta->fecha->format('Y-m-d') }}|Total:{{ $venta->total }}|Nit:900123456" alt="Código QR Factura">
            <p style="font-size: 10px;">CUFE: (Simulado)</p>
        </div>
    </div>

    <div class="footer">
        Software: PrismaNova v1.0 - Desarrollado por Trae AI
    </div>
</div>

</body>
</html>
