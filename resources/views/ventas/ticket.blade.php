<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $venta->id_venta }}</title>
    <!-- Google Fonts: Inter & Courier Prime -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/ticket.css') }}" rel="stylesheet">
</head>
<body>
    <div class="ticket-wrapper">
        <div class="ticket-container">
            <div class="ticket-content">
                <div class="header">
                    <h2>{{ $configuracion['nombre_tienda'] ?? 'PRISMANOVA' }}</h2>
                    <p>{{ $configuracion['direccion_tienda'] ?? 'Dirección Principal' }}</p>
                    <p>Tel: {{ $configuracion['telefono_contacto'] ?? '---' }}</p>
                    @if(isset($configuracion['email_contacto']))
                    <p>{{ $configuracion['email_contacto'] }}</p>
                    @endif
                </div>

                <div class="info">
                    <p><strong>Ticket:</strong> <span>#{{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}</span></p>
                    <p><strong>Fecha:</strong> <span>{{ $venta->fecha->format('d/m/Y H:i') }}</span></p>
                    <p><strong>Cajero:</strong> <span>{{ $venta->usuario->nombre ?? 'Sistema' }}</span></p>
                    <p><strong>Cliente:</strong> <span>{{ $venta->cliente->nombre ?? 'Público General' }}</span></p>
                    @if($venta->cliente && $venta->cliente->documento)
                    <p><strong>DOC:</strong> <span>{{ $venta->cliente->documento }}</span></p>
                    @endif
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="col-desc">Desc</th>
                            <th class="col-cant">Cant</th>
                            <th class="col-pu">P.U.</th>
                            <th class="col-total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'Item eliminado' }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-right">{{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-right">{{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="subtotal-row">
                            <td colspan="3" class="text-right">Subtotal:</td>
                            <td class="text-right">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->subtotal, 2) }}</td>
                        </tr>
                        @if($venta->descuento > 0)
                        <tr class="discount-row">
                            <td colspan="3" class="text-right">Descuento:</td>
                            <td class="text-right">-{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->descuento, 2) }}</td>
                        </tr>
                        @endif
                        @if($venta->impuesto > 0)
                        <tr class="tax-row">
                            <td colspan="3" class="text-right">IVA/Impuesto:</td>
                            <td class="text-right">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->impuesto, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td colspan="3" class="text-right">TOTAL:</td>
                            <td class="text-right">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($venta->total, 2) }}</td>
                        </tr>
                        <tr class="payment-method-row">
                            <td colspan="4" class="text-center" style="padding-top: 10px; font-size: 0.9em; font-style: italic;">
                                Método de Pago: {{ $venta->metodo_pago }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="footer">
                    <p>{{ $configuracion['mensaje_ticket'] ?? '¡Gracias por su compra!' }}</p>
                    <p style="margin-top: 5px; font-size: 0.7rem;">Generado por PrismaNova POS</p>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('ventas.create') }}" class="btn-back">Volver</a>
            <button onclick="window.print()" class="btn-print">Imprimir Ticket</button>
        </div>
    </div>
</body>
</html>
