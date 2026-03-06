<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Bitacora;

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
        $filtro = request('filtro');
        
        // Cargar productos con relaciones para optimizar consultas (Eager Loading)
        $query = Producto::with(['categoria','proveedor']);
            
        if ($q) {
            $query->where(function($w) use ($q){
                $w->where('nombre','like',"%$q%")
                  ->orWhere('codigo_barras','like',"%$q%");
            });
        }

        if ($filtro === 'stock_bajo') {
            $query->whereColumn('stock', '<=', 'stock_minimo');
        }
            
        $productos = $query->orderBy('id_producto','desc')->paginate(10);
            
        return view('productos.index', compact('productos'));
    }

    /**
     * Muestra la vista para imprimir la etiqueta de código de barras.
     * 
     * @param Producto $producto El producto del cual generar la etiqueta.
     * @return \Illuminate\View\View
     */
    public function etiqueta(Producto $producto)
    {
        return view('productos.etiqueta', compact('producto'));
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
        try {
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
            
            $producto = Producto::create($data);
            Bitacora::registrar('CREATE', 'productos', $producto->id_producto, 'Producto creado: ' . $producto->nombre);
            
            return redirect()->route('productos.index')->with('success','Producto creado');
        } catch (\Exception $e) {
            \Log::error('Error al crear producto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear producto: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();
        return view('productos.edit', compact('producto','categorias','proveedores'));
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            $data = $request->validated();
            
            // Normalizar valores nulos
            if (empty($data['id_proveedor'])) {
                $data['id_proveedor'] = null;
            }
            if (empty($data['fecha_vencimiento'])) {
                $data['fecha_vencimiento'] = null;
            }

            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($producto->imagen) {
                    Storage::disk('public')->delete($producto->imagen);
                }
                $data['imagen'] = $request->file('imagen')->store('productos', 'public');
            }

            // Limpiar formato de moneda en precios si vienen como string (e.g. "$ 1000")
            // NOTA: No eliminar el punto decimal si ya es un formato válido (e.g. "10.50")
            if (isset($data['precio_compra'])) {
                $val = str_replace(['$', 'S/', ' '], '', (string)$data['precio_compra']);
                // Si tiene coma como decimal, reemplazar por punto
                if (strpos($val, ',') !== false && strpos($val, '.') === false) {
                    $val = str_replace(',', '.', $val);
                }
                $data['precio_compra'] = (float) $val;
            }
            if (isset($data['precio_venta'])) {
                $val = str_replace(['$', 'S/', ' '], '', (string)$data['precio_venta']);
                // Si tiene coma como decimal, reemplazar por punto
                if (strpos($val, ',') !== false && strpos($val, '.') === false) {
                    $val = str_replace(',', '.', $val);
                }
                $data['precio_venta'] = (float) $val;
            }

            $producto->update($data);
            Bitacora::registrar('UPDATE', 'productos', $producto->id_producto, 'Producto actualizado');

            return redirect()->route('productos.index')->with('success','Producto actualizado');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar producto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar producto: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Envía un producto a la papelera (Soft Delete).
     */
    public function destroy(Producto $producto)
    {
        $producto->delete(); // Soft delete gracias al trait en el modelo
        Bitacora::registrar('DELETE', 'productos', $producto->id_producto, 'Producto enviado a papelera');
        return redirect()->route('productos.index')->with('success', 'Producto enviado a la papelera');
    }

    /**
     * Muestra los productos en la papelera de reciclaje.
     */
    public function papelera()
    {
        $productos = Producto::onlyTrashed()->with(['categoria','proveedor'])->paginate(10);
        return view('productos.papelera', compact('productos'));
    }

    /**
     * Restaura un producto de la papelera.
     */
    public function restaurar($id)
    {
        $producto = Producto::onlyTrashed()->findOrFail($id);
        $producto->restore();
        Bitacora::registrar('RESTORE', 'productos', $id, 'Producto restaurado');
        return redirect()->route('productos.papelera')->with('success', 'Producto restaurado correctamente');
    }

    /**
     * Elimina permanentemente un producto de la papelera.
     * Verifica que no tenga historial de ventas o compras para no romper la integridad.
     */
    public function forceDelete($id)
    {
        $producto = Producto::onlyTrashed()->findOrFail($id);
        
        // Verificar integridad referencial manualmente
        if ($producto->detallesVenta()->exists() || $producto->detallesCompra()->exists()) {
             return redirect()->route('productos.papelera')->with('error', 'No se puede eliminar permanentemente: El producto tiene historial de ventas o compras. Solo puede permanecer desactivado.');
        }

        try {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $producto->forceDelete();
            Bitacora::registrar('FORCE_DELETE', 'productos', $id, 'Producto eliminado permanentemente');
            return redirect()->route('productos.papelera')->with('success', 'Producto eliminado permanentemente');
        } catch (\Exception $e) {
             return redirect()->route('productos.papelera')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
