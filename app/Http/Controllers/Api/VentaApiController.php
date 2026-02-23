<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaApiController extends Controller
{
    public static function computeTotals(array $items): array
    {
        $subtotal = 0.0;
        $descuentoAutoLineas = 0.0;
        foreach ($items as $it) {
            $cantidad = (int)$it['cantidad'];
            $precio = (float)$it['precio'];
            $lineaSubtotal = $precio * $cantidad;
            $subtotal += $lineaSubtotal;
            if ($cantidad >= 12) {
                $descuentoAutoLineas += round($lineaSubtotal * 0.05, 2);
            }
        }
        $descuentoAutoTiers = 0.0;
        if ($subtotal >= 200) {
            $descuentoAutoTiers = round($subtotal * 0.10, 2);
        } elseif ($subtotal >= 100) {
            $descuentoAutoTiers = round($subtotal * 0.05, 2);
        }
        $impuesto = 0.0;
        $descuento = $descuentoAutoLineas + $descuentoAutoTiers;
        $total = max(0, $subtotal - $descuento + $impuesto);
        return [
            'subtotal' => round($subtotal, 2),
            'descuento' => round($descuento, 2),
            'impuesto' => round($impuesto, 2),
            'total' => round($total, 2),
        ];
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }
        $rol = $user->rol;
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'required|string|max:50',
            'id_cliente' => 'nullable|integer|exists:clientes,id_cliente',
            'cupon' => 'nullable|string|max:50',
        ]);

        $idCliente = null;
        if ($rol === 'cliente') {
            $cli = Cliente::where('email', $user->email)->orWhere('documento',$user->documento)->first();
            if ($cli) {
                $idCliente = $cli->id_cliente;
            }
        } else {
            $idCliente = $data['id_cliente'] ?? null;
        }

        DB::beginTransaction();
        try {
            $detalles = [];
            $itemsCalculo = [];
            foreach ($data['items'] as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['id_producto']);
                if ($producto->stock < $item['cantidad']) {
                    throw new \RuntimeException('Stock insuficiente para producto '.$producto->nombre);
                }
                $precio = $producto->precio_venta;
                $itemsCalculo[] = ['precio' => $precio, 'cantidad' => $item['cantidad']];
                $detalles[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio' => $precio,
                    'subtotal' => $precio * $item['cantidad'],
                ];
                $producto->decrement('stock', $item['cantidad']);
            }
            $tot = self::computeTotals($itemsCalculo);
            if (!empty($data['cupon'])) {
                $cupon = \App\Models\Cupon::where('codigo', $data['cupon'])->first();
                if ($cupon && $cupon->esValido()) {
                    $extra = $cupon->tipo === 'porcentaje'
                        ? round($tot['subtotal'] * ($cupon->valor / 100), 2)
                        : (float)$cupon->valor;
                    $tot['descuento'] = round($tot['descuento'] + $extra, 2);
                    $tot['total'] = max(0, $tot['subtotal'] - $tot['descuento'] + $tot['impuesto']);
                    $cupon->increment('usos');
                }
            }

            $venta = Venta::create([
                'id_cliente' => $idCliente,
                'id_usuario' => $user->id_usuario,
                'fecha' => now(),
                'subtotal' => $tot['subtotal'],
                'descuento' => $tot['descuento'],
                'impuesto' => $tot['impuesto'],
                'total' => $tot['total'],
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'completada',
            ]);

            foreach ($detalles as $d) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $d['producto']->id_producto,
                    'cantidad' => $d['cantidad'],
                    'precio_unitario' => $d['precio'],
                    'subtotal' => $d['subtotal'],
                ]);
            }

            DB::commit();
            $venta->load('detalles');
            return response()->json($venta, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
