<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $q = request('q');
        $productos = Producto::with(['categoria','proveedor'])
            ->when($q, function($w) use ($q){
                $w->where('nombre','like',"%$q%")
                  ->orWhere('codigo_barras','like',"%$q%");
            })
            ->orderBy('id_producto','desc')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();
        return view('productos.create', compact('categorias','proveedores'));
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria','proveedor']);
        return view('productos.show', compact('producto'));
    }

    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        Producto::create($data);
        return redirect()->route('productos.index')->with('success','Producto creado');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();
        return view('productos.edit', compact('producto','categorias','proveedores'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        $producto->update($data);
        return redirect()->route('productos.index')->with('success','Producto actualizado');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return redirect()->route('productos.index')->with('success','Producto eliminado');
    }
}
