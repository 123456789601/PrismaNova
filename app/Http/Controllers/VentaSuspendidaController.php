<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VentaSuspendida;
use Illuminate\Support\Facades\Auth;

class VentaSuspendidaController extends Controller
{
    /**
     * List current user's suspended sales.
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
     * Store a suspended sale.
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
     * Delete a suspended sale (e.g., when resumed).
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
