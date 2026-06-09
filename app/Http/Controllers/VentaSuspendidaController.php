<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VentaSuspendida;
use Illuminate\Support\Facades\Auth;

/**
 * Class VentaSuspendidaController
 * 
 * Gestiona las ventas suspendidas (en espera) del usuario actual.
 * Permite poner ventas en pausa y recuperarlas posteriormente.
 */
class VentaSuspendidaController extends Controller
{
    /**
     * Lista las ventas suspendidas del usuario actual.
     *
     * @return \Illuminate\Http\JsonResponse Lista de ventas en espera.
     */
    public function index()
    {
        $ventas = VentaSuspendida::with('cliente')
            ->where('id_usuario', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($ventas);
    }

    /**
     * Almacena una nueva venta suspendida.
     * 
     * Guarda el contenido del carrito, total y cliente asociado
     * para poder recuperarla posteriormente.
     *
     * @param  \Illuminate\Http\Request  $request Solicitud con contenido, total y cliente.
     * @return \Illuminate\Http\JsonResponse Respuesta con la venta creada.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|array',
            'total' => 'required|numeric',
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'nota' => 'nullable|string|max:255',
        ]);

        $venta = VentaSuspendida::create([
            'id_usuario' => Auth::id(),
            'id_cliente' => $request->id_cliente,
            'contenido' => $request->contenido,
            'total' => $request->total,
            'nota' => $request->nota,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Venta puesta en espera',
            'venta' => $venta
        ]);
    }

    /**
     * Elimina una venta suspendida (ej. cuando se recupera).
     * 
     * Solo permite eliminar ventas del usuario autenticado.
     *
     * @param  int  $id ID de la venta suspendida.
     * @return \Illuminate\Http\JsonResponse Respuesta de éxito.
     */
    public function destroy($id)
    {
        $venta = VentaSuspendida::where('id_usuario', Auth::id())
            ->where('id_venta_suspendida', $id)
            ->firstOrFail();
            
        $venta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Venta recuperada/eliminada'
        ]);
    }
}
