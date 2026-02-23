<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Http\Requests\StoreCuponRequest;
use App\Http\Requests\UpdateCuponRequest;

class CuponController extends Controller
{
    public function index()
    {
        $q = request('q');
        $cupones = Cupon::when($q, function($w) use ($q){
            $w->where('codigo','like',"%$q%");
        })->orderBy('id_cupon','desc')->paginate(10);
        return view('cupones.index', compact('cupones'));
    }

    public function create()
    {
        return view('cupones.create');
    }

    public function store(StoreCuponRequest $request)
    {
        Cupon::create($request->validated());
        return redirect()->route('cupones.index')->with('success','Cupón creado');
    }

    public function edit(Cupon $cupone)
    {
        return view('cupones.edit', ['cupon' => $cupone]);
    }

    public function update(UpdateCuponRequest $request, Cupon $cupone)
    {
        $cupone->update($request->validated());
        return redirect()->route('cupones.index')->with('success','Cupón actualizado');
    }

    public function destroy(Cupon $cupone)
    {
        $cupone->delete();
        return redirect()->route('cupones.index')->with('success','Cupón eliminado');
    }
}
