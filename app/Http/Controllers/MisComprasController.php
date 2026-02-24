<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

/**
 * Class MisComprasController
 * 
 * Gestiona el historial de compras del cliente autenticado.
 * Permite a los usuarios finales ver sus pedidos y facturas.
 */
class MisComprasController extends Controller
{
    /**
     * Obtiene el perfil de cliente asociado al usuario autenticado.
     * 
     * Busca por email o documento del usuario actual.
     *
     * @return Cliente|null
     */
    protected function cliente()
    {
        $u = Auth::user();
        return Cliente::where('email', $u->email)->orWhere('documento', $u->documento)->first();
    }

    /**
     * Muestra el listado de compras realizadas por el cliente.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cliente = $this->cliente();
        if (!$cliente) {
            return view('mis_compras.index', ['ventas' => collect(), 'cliente' => null]);
        }
        $ventas = Venta::with('detalles')->where('id_cliente', $cliente->id_cliente)->orderBy('id_venta', 'desc')->paginate(10);
        return view('mis_compras.index', compact('ventas', 'cliente'));
    }

    /**
     * Muestra el detalle de una compra específica.
     * 
     * Verifica que la compra pertenezca al cliente autenticado.
     *
     * @param Venta $venta
     * @return \Illuminate\View\View
     */
    public function show(Venta $venta)
    {
        $cliente = $this->cliente();
        if (!$cliente || $venta->id_cliente !== $cliente->id_cliente) {
            abort(403);
        }
        $venta->load(['detalles.producto', 'usuario', 'cliente', 'metodoPago']);
        return view('mis_compras.show', compact('venta'));
    }

    /**
     * Genera la vista de factura para una compra del cliente.
     * 
     * Verifica la propiedad de la compra antes de mostrarla.
     *
     * @param Venta $venta
     * @return \Illuminate\View\View
     */
    public function factura(Venta $venta)
    {
        $cliente = $this->cliente();
        if (!$cliente || $venta->id_cliente !== $cliente->id_cliente) {
            abort(403);
        }
        $venta->load(['detalles.producto', 'usuario', 'cliente', 'metodoPago']);
        return view('ventas.factura', compact('venta'));
    }
}
