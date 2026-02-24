<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Class CarritoController
 * 
 * Controlador API para la gestión del carrito de compras en sesión.
 * Maneja operaciones AJAX para agregar, actualizar y eliminar productos.
 */
class CarritoController extends Controller
{
    /**
     * Genera la URL de la imagen del producto o un placeholder.
     *
     * @param string|null $path
     * @return string
     */
    protected function imgUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : asset('img/placeholder-producto.svg');
    }

    /**
     * Recupera el carrito actual de la sesión.
     *
     * @param Request $request
     * @return array
     */
    protected function loadCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    /**
     * Guarda el estado actual del carrito en la sesión.
     *
     * @param Request $request
     * @param array $cart
     * @return void
     */
    protected function saveCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    /**
     * Calcula la cantidad total de items en el carrito.
     *
     * @param array $cart
     * @return int
     */
    protected function count(array $cart): int
    {
        return array_sum(array_map(fn($i)=> (int)($i['cantidad'] ?? 0), $cart));
    }

    /**
     * Devuelve el contenido actual del carrito en formato JSON.
     * 
     * Calcula subtotales y total general.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Agrega un producto al carrito.
     * 
     * Si el producto ya existe, incrementa su cantidad.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Actualiza la cantidad de un producto en el carrito.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Elimina un producto del carrito.
     *
     * @param Request $request
     * @param int $idProducto
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, int $idProducto)
    {
        $cart = $this->loadCart($request);
        $key = (string)$idProducto;
        unset($cart[$key]);
        $this->saveCart($request, $cart);
        return response()->json(['ok'=>true,'count'=>$this->count($cart)]);
    }
}
