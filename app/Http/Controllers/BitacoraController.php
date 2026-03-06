<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Muestra el listado de registros de la bitácora.
     * Solo accesible por administradores.
     */
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->orderBy('created_at', 'desc');

        if ($request->has('usuario_id') && $request->usuario_id) {
            $query->where('id_usuario', $request->usuario_id);
        }

        if ($request->has('fecha') && $request->fecha) {
            $query->whereDate('created_at', $request->fecha);
        }
        
        if ($request->has('accion') && $request->accion) {
            $query->where('accion', $request->accion);
        }

        $registros = $query->paginate(20);
        $usuarios = Usuario::all(); // Para el filtro

        return view('bitacora.index', compact('registros', 'usuarios'));
    }
}
