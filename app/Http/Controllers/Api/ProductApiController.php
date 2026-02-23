<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $q = Producto::query()->where('estado','activo');
        if ($s = $request->query('search')) {
            $q->where(function($w) use ($s){
                $w->where('nombre','like',"%$s%")
                  ->orWhere('descripcion','like',"%$s%")
                  ->orWhere('codigo_barras','like',"%$s%");
            });
        }
        $productos = $q->orderBy('id_producto','desc')->paginate(12);
        $productos->getCollection()->transform(function($p){
            $p->imagen_url = $p->imagen ? asset('storage/'.$p->imagen) : null;
            return $p;
        });
        return response()->json($productos);
    }

    public function show(Producto $producto)
    {
        $producto->imagen_url = $producto->imagen ? asset('storage/'.$producto->imagen) : null;
        return response()->json($producto);
    }

    public function store(Request $request)
    {
        $this->authorizeAction($request);
        $data = $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('products','public');
                $data['imagen'] = $path;
            }
            $producto = Producto::create($data);
            DB::commit();
            $producto->imagen_url = $producto->imagen ? asset('storage/'.$producto->imagen) : null;
            return response()->json($producto, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear'], 422);
        }
    }

    public function update(Request $request, Producto $producto)
    {
        $this->authorizeAction($request);
        $data = $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $producto->id_producto . ',id_producto',
            'nombre' => 'sometimes|required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'sometimes|exists:categorias,id_categoria',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'sometimes|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('imagen')) {
                if ($producto->imagen) {
                    Storage::disk('public')->delete($producto->imagen);
                }
                $data['imagen'] = $request->file('imagen')->store('products','public');
            }
            $producto->update($data);
            DB::commit();
            $producto->imagen_url = $producto->imagen ? asset('storage/'.$producto->imagen) : null;
            return response()->json($producto);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo actualizar'], 422);
        }
    }

    public function destroy(Request $request, Producto $producto)
    {
        $this->authorizeAction($request);
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return response()->json(['deleted' => true]);
    }

    protected function authorizeAction(Request $request)
    {
        $rol = optional($request->user())->rol;
        if (!in_array($rol, ['admin','cajero','bodeguero'])) {
            abort(403);
        }
    }
}
