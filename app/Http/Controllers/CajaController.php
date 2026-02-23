<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Http\Requests\StoreMovimientoCajaRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::orderBy('id_caja','desc')->paginate(10);
        return view('caja.index', compact('cajas'));
    }

    public function show(Caja $caja)
    {
        $caja->load('movimientos');
        return view('caja.show', compact('caja'));
    }

    public function abrir()
    {
        $abierta = Caja::where('estado','abierta')->first();
        if ($abierta) {
            return back()->with('error','Ya existe una caja abierta');
        }
        $caja = Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 0,
            'estado' => 'abierta',
        ]);
        return redirect()->route('caja.show',$caja)->with('success','Caja abierta');
    }

    public function cerrar(Caja $caja)
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error','La caja no está abierta');
        }
        try {
            DB::transaction(function () use ($caja) {
                $caja->load('movimientos');
                $saldo = $caja->monto_inicial;
                foreach ($caja->movimientos as $m) {
                    if ($m->tipo === 'ingreso') {
                        $saldo += $m->monto;
                    } else {
                        $saldo -= $m->monto;
                    }
                }
                $caja->monto_final = $saldo;
                $caja->fecha_cierre = now();
                $caja->estado = 'cerrada';
                $caja->save();
            });
        } catch (Exception $e) {
            return back()->with('error','No se pudo cerrar: '.$e->getMessage());
        }
        return back()->with('success','Caja cerrada');
    }

    public function storeMovimiento(StoreMovimientoCajaRequest $request, Caja $caja)
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error','Solo se registran movimientos en caja abierta');
        }
        MovimientoCaja::create(array_merge(
            $request->validated(),
            ['id_caja' => $caja->id_caja, 'fecha' => now()]
        ));
        return back()->with('success','Movimiento registrado');
    }
}
