<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;

/**
 * Class CategoriaController
 * 
 * Gestiona las categorías de productos.
 * Permite organizar el inventario mediante la clasificación de productos.
 */
class CategoriaController extends Controller
{
    /**
     * Muestra el listado de categorías.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categorias = Categoria::orderBy('id_categoria','desc')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Muestra los detalles de una categoría.
     *
     * @param Categoria $categoria
     * @return \Illuminate\View\View
     */
    public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     *
     * @param StoreCategoriaRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());
        return redirect()->route('categorias.index')->with('success','Categoría creada');
    }

    /**
     * Muestra el formulario para editar una categoría.
     *
     * @param Categoria $categoria
     * @return \Illuminate\View\View
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualiza la información de una categoría.
     *
     * @param UpdateCategoriaRequest $request
     * @param Categoria $categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());
        return redirect()->route('categorias.index')->with('success','Categoría actualizada');
    }

    /**
     * Elimina una categoría del sistema.
     *
     * @param Categoria $categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success','Categoría eliminada');
    }
}
