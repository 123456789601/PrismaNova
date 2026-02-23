<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    protected function imgUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : asset('img/placeholder-producto.svg');
    }

    protected function loadCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    protected function saveCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    protected function count(array $cart): int
    {
        return array_sum(array_map(fn($i)=> (int)($i['cantidad'] ?? 0), $cart));
    }

    public function list(Request $request)
    {
        $cart = $this->loadCart($request);
        $total = 0.0;
        foreach ($cart as &$i) {
            $i['subtotal'] = round($i['precio'] * $i['cantidad'], 2);
            $total += $i['subtotal'];
        }
        return response()->json([
            'items' => array_values($cart),
            'count' => $this->count($cart),
            'total' => round($total, 2),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|integer|exists:productos,id_producto',
        ]);
        $producto = Producto::findOrFail($data['id_producto']);
        $cart = $this->loadCart($request);
        $key = (string)$producto->id_producto;
        if (!isset($cart[$key])) {
            $cart[$key] = [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'precio' => (float)$producto->precio_venta,
                'imagen' => $this->imgUrl($producto->imagen),
                'cantidad' => 0,
            ];
        }
        $cart[$key]['cantidad'] = (int)$cart[$key]['cantidad'] + 1;
        $this->saveCart($request, $cart);
        return response()->json(['ok'=>true,'count'=>$this->count($cart)]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);
        $cart = $this->loadCart($request);
        $key = (string)$data['id_producto'];
        if (!isset($cart[$key])) {
            return response()->json(['error'=>'No existe en carrito'], 404);
        }
        $cart[$key]['cantidad'] = (int)$data['cantidad'];
        $this->saveCart($request, $cart);
        return response()->json(['ok'=>true,'count'=>$this->count($cart)]);
    }

    public function remove(Request $request, int $idProducto)
    {
        $cart = $this->loadCart($request);
        $key = (string)$idProducto;
        unset($cart[$key]);
        $this->saveCart($request, $cart);
        return response()->json(['ok'=>true,'count'=>$this->count($cart)]);
    }
}
