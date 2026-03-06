<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Bitacora;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;

/**
 * Class ProveedorController
 * 
 * Controlador para la administración de proveedores.
 * Gestiona el registro y actualización de empresas proveedoras.
 */
class ProveedorController extends Controller
{
    /**
     * Muestra el listado de proveedores registrados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $proveedores = Proveedor::orderBy('id_proveedor','desc')->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Muestra el formulario para registrar un nuevo proveedor.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Muestra la información detallada de un proveedor.
     *
     * @param Proveedor $proveedor
     * @return \Illuminate\View\View
     */
    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Guarda un nuevo proveedor en la base de datos.
     *
     * @param StoreProveedorRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProveedorRequest $request)
    {
        Proveedor::create($request->validated());
        return redirect()->route('proveedores.index')->with('success','Proveedor creado');
    }

    /**
     * Muestra el formulario para editar un proveedor existente.
     *
     * @param Proveedor $proveedor
     * @return \Illuminate\View\View
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualiza la información de un proveedor.
     *
     * @param UpdateProveedorRequest $request
     * @param Proveedor $proveedor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        $proveedor->update($request->validated());
        Bitacora::registrar('UPDATE', 'proveedores', $proveedor->id_proveedor, 'Proveedor actualizado');
        return redirect()->route('proveedores.index')->with('success','Proveedor actualizado');
    }

    /**
     * Elimina un proveedor de la base de datos.
     *
     * @param Proveedor $proveedor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Proveedor $proveedor)
    {
        // Verificar integridad referencial
        if ($proveedor->compras()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene compras asociadas.');
        }
        if ($proveedor->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        $id = $proveedor->id_proveedor;
        $proveedor->delete();
        Bitacora::registrar('DELETE', 'proveedores', $id, 'Proveedor eliminado');
        return redirect()->route('proveedores.index')->with('success','Proveedor eliminado');
    }
}
