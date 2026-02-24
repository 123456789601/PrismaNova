<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductApiController
 * 
 * Controlador API para la gestión del catálogo de productos.
 * Soporta operaciones CRUD completas y manejo de imágenes.
 */
class ProductApiController extends Controller
{
    /**
     * Lista los productos activos con paginación y búsqueda.
     * 
     * Transforma la respuesta para incluir la URL completa de la imagen.
     *
     * @param Request $request Filtros: search (nombre, descripción, código de barras).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $q = Producto::query()->where('estado','activo');
        
        // Filtro de búsqueda general
        if ($s = $request->query('search')) {
            $q->where(function($w) use ($s){
                $w->where('nombre','like',"%$s%")
                  ->orWhere('descripcion','like',"%$s%")
                  ->orWhere('codigo_barras','like',"%$s%");
            });
        }
        
        $productos = $q->orderBy('id_producto','desc')->paginate(12);
        
        // Agregar URL completa de la imagen a cada producto
        $productos->getCollection()->transform(function($p){
            $p->imagen_url = $p->imagen ? asset('storage/'.$p->imagen) : null;
            return $p;
        });
        
        return response()->json($productos);
    }

    /**
     * Muestra los detalles de un producto específico.
     *
     * @param Producto $producto
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Producto $producto)
    {
        $producto->imagen_url = $producto->imagen ? asset('storage/'.$producto->imagen) : null;
        return response()->json($producto);
    }

    /**
     * Crea un nuevo producto en la base de datos.
     * 
     * Maneja la subida de imágenes y la normalización de campos opcionales.
     * Requiere rol admin, cajero o bodeguero.
     *
     * @param Request $request Datos del producto.
     * @return \Illuminate\Http\JsonResponse
     */
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
            // Normalizar campos opcionales que pueden venir vacíos
            if (empty($data['id_proveedor'])) {
                $data['id_proveedor'] = null;
            }
            if (empty($data['fecha_vencimiento'])) {
                $data['fecha_vencimiento'] = null;
            }
            
            // Procesar subida de imagen
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

    /**
     * Actualiza un producto existente.
     * 
     * Maneja validación, reemplazo de imagen (eliminando la anterior) y normalización de datos.
     *
     * @param Request $request Datos a actualizar.
     * @param Producto $producto Producto a editar.
     * @return \Illuminate\Http\JsonResponse
     */
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
            // Normalizar campos opcionales si están presentes en el request
            if (array_key_exists('id_proveedor', $data) && empty($data['id_proveedor'])) {
                $data['id_proveedor'] = null;
            }
            if (array_key_exists('fecha_vencimiento', $data) && empty($data['fecha_vencimiento'])) {
                $data['fecha_vencimiento'] = null;
            }
            
            // Reemplazar imagen si se sube una nueva
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior para ahorrar espacio
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

    /**
     * Elimina un producto y su imagen asociada.
     *
     * @param Request $request
     * @param Producto $producto
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Producto $producto)
    {
        $this->authorizeAction($request);
        
        // Eliminar archivo físico de la imagen
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        
        $producto->delete();
        
        return response()->json(['deleted' => true]);
    }

    /**
     * Verifica que el usuario tenga permisos para modificar el catálogo.
     * 
     * Roles permitidos: admin, cajero, bodeguero.
     *
     * @param Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeAction(Request $request)
    {
        $rol = optional($request->user())->rol;
        if (!in_array($rol, ['admin','cajero','bodeguero'])) {
            abort(403);
        }
    }
}
