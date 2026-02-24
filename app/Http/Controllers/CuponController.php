<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Http\Requests\StoreCuponRequest;
use App\Http\Requests\UpdateCuponRequest;

/**
 * Class CuponController
 * 
 * Gestiona los cupones de descuento.
 * Permite crear y administrar códigos promocionales para las ventas.
 */
class CuponController extends Controller
{
    /**
     * Muestra el listado de cupones con búsqueda por código.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $q = request('q');
        $cupones = Cupon::when($q, function($w) use ($q){
            $w->where('codigo','like',"%$q%");
        })->orderBy('id_cupon','desc')->paginate(10);
        return view('cupones.index', compact('cupones'));
    }

    /**
     * Muestra el formulario para crear un nuevo cupón.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cupones.create');
    }

    /**
     * Almacena un nuevo cupón en la base de datos.
     *
     * @param StoreCuponRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCuponRequest $request)
    {
        Cupon::create($request->validated());
        return redirect()->route('cupones.index')->with('success','Cupón creado');
    }

    /**
     * Muestra el formulario para editar un cupón existente.
     *
     * @param Cupon $cupone
     * @return \Illuminate\View\View
     */
    public function edit(Cupon $cupone)
    {
        return view('cupones.edit', ['cupon' => $cupone]);
    }

    /**
     * Actualiza la información de un cupón.
     *
     * @param UpdateCuponRequest $request
     * @param Cupon $cupone
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCuponRequest $request, Cupon $cupone)
    {
        $cupone->update($request->validated());
        return redirect()->route('cupones.index')->with('success','Cupón actualizado');
    }

    /**
     * Elimina un cupón del sistema.
     *
     * @param Cupon $cupone
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cupon $cupone)
    {
        $cupone->delete();
        return redirect()->route('cupones.index')->with('success','Cupón eliminado');
    }
}
