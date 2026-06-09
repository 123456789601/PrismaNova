<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Http\Requests\StoreCuponRequest;
use App\Http\Requests\UpdateCuponRequest;
use App\Models\Bitacora;

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
     * @param Cupon $cupon
     * @return \Illuminate\View\View
     */
    public function edit(Cupon $cupon)
    {
        return view('cupones.edit', compact('cupon'));
    }

    /**
     * Actualiza la información de un cupón.
     *
     * @param UpdateCuponRequest $request
     * @param Cupon $cupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCuponRequest $request, Cupon $cupon)
    {
        $cupon->update($request->validated());
        Bitacora::registrar('UPDATE', 'cupones', $cupon->id_cupon, 'Cupón actualizado');
        return redirect()->route('cupones.index')->with('success','Cupón actualizado');
    }

    /**
     * Elimina un cupón del sistema.
     *
     * @param Cupon $cupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cupon $cupon)
    {
        $id = $cupon->id_cupon;
        $cupon->delete();
        Bitacora::registrar('DELETE', 'cupones', $id, 'Cupón eliminado');
        return redirect()->route('cupones.index')->with('success','Cupón eliminado');
    }
}
