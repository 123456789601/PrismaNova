<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class MisComprasController extends Controller
{
    protected function cliente()
    {
        $u = Auth::user();
        return Cliente::where('email', $u->email)->orWhere('documento', $u->documento)->first();
    }

    public function index()
    {
        $cliente = $this->cliente();
        if (!$cliente) {
            return view('mis_compras.index', ['ventas' => collect(), 'cliente' => null]);
        }
        $ventas = Venta::with('detalles')->where('id_cliente', $cliente->id_cliente)->orderBy('id_venta', 'desc')->paginate(10);
        return view('mis_compras.index', compact('ventas', 'cliente'));
    }

    public function show(Venta $venta)
    {
        $cliente = $this->cliente();
        if (!$cliente || $venta->id_cliente !== $cliente->id_cliente) {
            abort(403);
        }
        $venta->load(['detalles.producto', 'usuario', 'cliente', 'metodoPago']);
        return view('mis_compras.show', compact('venta'));
    }
}
