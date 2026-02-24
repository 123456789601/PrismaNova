<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;

/**
 * Class ClienteController
 * 
 * Controlador encargado de la gestión de clientes.
 * Permite realizar operaciones CRUD sobre los registros de clientes.
 */
class ClienteController extends Controller
{
    /**
     * Muestra el listado de clientes registrados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $clientes = Cliente::orderBy('id_cliente','desc')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Muestra la información detallada de un cliente.
     *
     * @param Cliente $cliente
     * @return \Illuminate\View\View
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Almacena un nuevo cliente en la base de datos.
     *
     * @param StoreClienteRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());
        return redirect()->route('clientes.index')->with('success','Cliente creado');
    }

    /**
     * Muestra el formulario para editar un cliente existente.
     *
     * @param Cliente $cliente
     * @return \Illuminate\View\View
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza la información de un cliente.
     *
     * @param UpdateClienteRequest $request
     * @param Cliente $cliente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.index')->with('success','Cliente actualizado');
    }

    /**
     * Elimina un cliente de la base de datos.
     *
     * @param Cliente $cliente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success','Cliente eliminado');
    }
}
