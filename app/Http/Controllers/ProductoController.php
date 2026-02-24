<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductoController
 * 
 * Controlador web para la administración de productos.
 * Gestiona las vistas y lógica de negocio para el catálogo de productos (CRUD).
 */
class ProductoController extends Controller
{
    /**
     * Muestra el listado de productos con opciones de búsqueda.
     * 
     * @return \Illuminate\View\View Vista con la tabla de productos paginada.
     */
    public function index()
    {
        $q = request('q');
        
        // Cargar productos con relaciones para optimizar consultas (Eager Loading)
        $productos = Producto::with(['categoria','proveedor'])
            ->when($q, function($w) use ($q){
                $w->where('nombre','like',"%$q%")
                  ->orWhere('codigo_barras','like',"%$q%");
            })
            ->orderBy('id_producto','desc')->paginate(10);
            
        return view('productos.index', compact('productos'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     * 
     * Carga las listas de categorías y proveedores para los selectores.
     *
     * @return \Illuminate\View\View Vista del formulario de creación.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();
        return view('productos.create', compact('categorias','proveedores'));
    }

    /**
     * Muestra la información detallada de un producto específico.
     *
     * @param Producto $producto
     * @return \Illuminate\View\View Vista de detalles.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria','proveedor']);
        return view('productos.show', compact('producto'));
    }

    /**
     * Almacena un producto recién creado en la base de datos.
     * 
     * Utiliza StoreProductoRequest para validación.
     * Normaliza campos opcionales y maneja la carga de imágenes.
     *
     * @param StoreProductoRequest $request Datos validados del formulario.
     * @return \Illuminate\Http\RedirectResponse Redirección al índice con mensaje de éxito.
     */
    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();
        
        // Normalizar valores nulos para claves foráneas y fechas opcionales
        if (empty($data['id_proveedor'])) {
            $data['id_proveedor'] = null;
        }
        if (empty($data['fecha_vencimiento'])) {
            $data['fecha_vencimiento'] = null;
        }
        
        // Asignar estado por defecto si no viene en el request
        if (empty($data['estado'])) {
            $data['estado'] = 'activo';
        }
        
        // Procesar imagen si se adjuntó
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        
        Producto::create($data);
        
        return redirect()->route('productos.index')->with('success','Producto creado');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     *
     * @param Producto $producto
     * @return \Illuminate\View\View Vista del formulario de edición.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();
        return view('productos.edit', compact('producto','categorias','proveedores'));
    }

    /**
     * Actualiza un producto específico en la base de datos.
     * 
     * Utiliza UpdateProductoRequest para validación.
     * Maneja el reemplazo de imágenes eliminando la anterior.
     *
     * @param UpdateProductoRequest $request Datos validados.
     * @param Producto $producto Producto a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();
        
        // Normalizar campos opcionales si existen en el array de datos
        if (array_key_exists('id_proveedor', $data) && empty($data['id_proveedor'])) {
            $data['id_proveedor'] = null;
        }
        if (array_key_exists('fecha_vencimiento', $data) && empty($data['fecha_vencimiento'])) {
            $data['fecha_vencimiento'] = null;
        }
        
        // Gestionar reemplazo de imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe físicamente
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        
        $producto->update($data);
        
        return redirect()->route('productos.index')->with('success','Producto actualizado');
    }

    /**
     * Elimina un producto de la base de datos.
     * 
     * También elimina la imagen asociada del almacenamiento.
     *
     * @param Producto $producto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Producto $producto)
    {
        // Limpiar archivo de imagen antes de borrar registro
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }
        
        $producto->delete();
        
        return redirect()->route('productos.index')->with('success','Producto eliminado');
    }
}
