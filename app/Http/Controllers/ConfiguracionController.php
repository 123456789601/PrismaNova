<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\Bitacora;
use Illuminate\Http\Request;

/**
 * Class ConfiguracionController
 * 
 * Gestiona la configuración global del sistema.
 * Permite editar variables clave como nombre de tienda, impuestos, etc.
 */
class ConfiguracionController extends Controller
{
    /**
     * Muestra el formulario de configuración con los valores actuales.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todas las configuraciones como array clave => valor
        $configuraciones = Configuracion::all();
        return view('configuracion.index', compact('configuraciones'));
    }

    /**
     * Actualiza las configuraciones del sistema.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token']);
        
        foreach ($data as $clave => $valor) {
            $config = Configuracion::where('clave', $clave)->first();
            if ($config) {
                // Solo actualizar si el valor cambió
                if ($config->valor !== $valor) {
                    $config->valor = $valor;
                    $config->save();
                }
            } else {
                // Opcional: Crear si no existe (aunque idealmente se crean en seed/migration)
                Configuracion::create([
                    'clave' => $clave,
                    'valor' => $valor
                ]);
            }
        }
        
        Bitacora::registrar('UPDATE', 'configuracion', 0, 'Configuración del sistema actualizada');
        
        return back()->with('success', 'Configuración actualizada correctamente');
    }
}
