<?php

namespace App\Http\Controllers;

class TiendaController extends Controller
{
    public function catalogo()
    {
        return view('tienda.catalogo');
    }

    public function carrito()
    {
        return view('tienda.carrito');
    }
}
