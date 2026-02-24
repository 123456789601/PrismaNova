<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Http\Requests\StoreCompraRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class CompraController
 * 
 * Gestiona el proceso de compras de productos a proveedores.
 * Controla el registro de compras, actualización de stock e historial.
 */
class CompraController extends Controller
{
    /**
     * Muestra el historial de compras realizadas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $compras = Compra::with(['proveedor','usuario'])->orderBy('id_compra','desc')->paginate(10);
        return view('compras.index', compact('compras'));
    }

    /**
     * Muestra el formulario para registrar una nueva compra.
     * 
     * Carga proveedores y productos activos para la selección.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $proveedores = Proveedor::where('estado','activo')->orderBy('nombre_empresa')->get();
        $productos = Producto::where('estado','activo')->orderBy('nombre')->get();
        return view('compras.create', compact('proveedores','productos'));
    }

    /**
     * Muestra los detalles de una compra específica.
     *
     * @param Compra $compra
     * @return \Illuminate\View\View
     */
    public function show(Compra $compra)
    {
        $compra->load(['proveedor','usuario','detalles.producto']);
        return view('compras.show', compact('compra'));
    }

    /**
     * Registra una nueva compra en el sistema.
     * 
     * Crea la cabecera de la compra, sus detalles y actualiza el stock de los productos.
     * Todo se realiza dentro de una transacción para garantizar consistencia.
     *
     * @param StoreCompraRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCompraRequest $request)
    {
        $data = $request->validated();
        try {
            DB::transaction(function () use ($data) {
                $subtotal = 0;
                $detalles = [];
                foreach ($data['id_producto'] as $i => $idProducto) {
                    $cantidad = (int)$data['cantidad'][$i];
                    $precio = (float)$data['precio_compra'][$i];
                    $linea = $cantidad * $precio;
                    $subtotal += $linea;
                    $detalles[] = [
                        'id_producto' => $idProducto,
                        'cantidad' => $cantidad,
                        'precio_compra' => $precio,
                        'subtotal' => $linea,
                    ];
                }
                $impuesto = isset($data['impuesto']) ? (float)$data['impuesto'] : 0;
                $total = $subtotal + $impuesto;

                $compra = Compra::create([
                    'id_proveedor' => $data['id_proveedor'],
                    'id_usuario' => Auth::user()->id_usuario,
                    'fecha' => $data['fecha'],
                    'subtotal' => $subtotal,
                    'impuesto' => $impuesto,
                    'total' => $total,
                    'estado' => 'recibida',
                ]);

                foreach ($detalles as $d) {
                    $d['id_compra'] = $compra->id_compra;
                    DetalleCompra::create($d);
                    $producto = Producto::lockForUpdate()->find($d['id_producto']);
                    $producto->stock = (int)$producto->stock + (int)$d['cantidad'];
                    $producto->save();
                }
            });
        } catch (Exception $e) {
            return back()->with('error', 'Error al registrar la compra: '.$e->getMessage())->withInput();
        }
        return redirect()->route('compras.index')->with('success', 'Compra registrada');
    }

    /**
     * Anula una compra existente.
     * 
     * Revierte el stock agregado por la compra y cambia su estado a 'anulada'.
     * Valida que no se genere stock negativo al revertir.
     *
     * @param Compra $compra
     * @return \Illuminate\Http\RedirectResponse
     */
    public function anular(Compra $compra)
    {
        if ($compra->estado === 'anulada') {
            return back()->with('error','La compra ya está anulada');
        }
        try {
            DB::transaction(function () use ($compra) {
                $compra->load('detalles');
                foreach ($compra->detalles as $det) {
                    $producto = Producto::lockForUpdate()->find($det->id_producto);
                    $nuevo = (int)$producto->stock - (int)$det->cantidad;
                    if ($nuevo < 0) {
                        throw new Exception('Stock negativo al anular compra');
                    }
                    $producto->stock = $nuevo;
                    $producto->save();
                }
                $compra->estado = 'anulada';
                $compra->save();
            });
        } catch (Exception $e) {
            return back()->with('error', 'No se pudo anular: '.$e->getMessage());
        }
        return back()->with('success','Compra anulada');
    }
}
