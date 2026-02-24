<?php

namespace App\Http\Controllers;

/**
 * Class TiendaController
 * 
 * Gestiona las vistas principales de la tienda online pública.
 * Sirve como punto de entrada para el catálogo y el carrito de compras.
 */
class TiendaController extends Controller
{
    /**
     * Muestra el catálogo de productos de la tienda.
     * 
     * Renderiza la vista principal donde los clientes pueden navegar productos.
     *
     * @return \Illuminate\View\View
     */
    public function catalogo()
    {
        return view('tienda.catalogo');
    }

    /**
     * Muestra la vista del carrito de compras.
     * 
     * Permite al usuario revisar los productos seleccionados antes de comprar.
     *
     * @return \Illuminate\View\View
     */
    public function carrito()
    {
        return view('tienda.carrito');
    }
}
