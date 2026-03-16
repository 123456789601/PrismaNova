<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Bitacora;

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
        try {
            $q = Producto::query()->where('estado','activo')->where('stock', '>', 0);
            
            // Filtro de búsqueda general
            if ($s = $request->query('search')) {
                $q->where(function($w) use ($s){
                    $w->where('nombre','like',"%$s%")
                      ->orWhere('descripcion','like',"%$s%")
                      ->orWhere('codigo_barras','like',"%$s%");
                });
            }
            
            $productos = $q->orderBy('id_producto','desc')->paginate(12);
            $productos->getCollection()->transform(function ($p) {
                $img = (string) ($p->imagen ?? '');
                if ($img !== '') {
                    if (preg_match('#^https?://#i', $img)) {
                        $p->imagen_url = $img;
                    } elseif (strpos($img, 'img/') === 0) {
                        $p->imagen_url = asset($img);
                    } else {
                        $p->imagen_url = asset('storage/'.$img);
                    }
                } else {
                    $p->imagen_url = asset('img/placeholder-producto.svg');
                }
                return $p;
            });

            return response()->json($productos);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error loading products: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
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
    public function store(StoreProductoRequest $request)
    {
        try {
            // $this->authorizeAction($request);
            
            $data = $request->validated();
            
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
                return response()->json(['error' => 'No se pudo crear: ' . $e->getMessage()], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
             return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
             return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
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
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $this->authorizeAction($request);
        
        $data = $request->validated();
        
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
            
            Bitacora::registrar('UPDATE', 'productos', $producto->id_producto, 'Producto actualizado via API');

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
        
        $id = $producto->id_producto;
        $producto->delete();
        Bitacora::registrar('DELETE', 'productos', $id, 'Producto eliminado via API');
        
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
        $rol = optional(optional($request->user())->rol)->nombre;
        if (!in_array($rol, ['admin','cajero','bodeguero'])) {
            abort(403);
        }
    }
}
