<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Models\Cliente;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class PerfilController
 * 
 * Gestiona el perfil del usuario autenticado.
 * Permite ver y actualizar la información personal, contraseña y preferencias.
 */
class PerfilController extends Controller
{
    /**
     * Muestra la vista del perfil del usuario.
     * 
     * Si el usuario es un cliente, también carga la información del modelo Cliente.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $usuario = Auth::user();
        // Buscar si existe un perfil de cliente asociado (por email o documento)
        $cliente = Cliente::where('email', $usuario->email)->orWhere('documento', $usuario->documento)->first();
        return view('perfil.index', compact('usuario','cliente'));
    }

    /**
     * Actualiza la información del perfil.
     * 
     * Actualiza tanto el usuario del sistema como el cliente asociado (si existe),
     * manteniendo la consistencia de datos (nombre, email, documento).
     *
     * @param UpdatePerfilRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePerfilRequest $request)
    {
        $usuario = Auth::user();
        $data = $request->validated();
        
        // Guardar valores originales para búsqueda de cliente
        $originalEmail = $usuario->email;
        $originalDocumento = $usuario->documento;

        // Actualizar usuario
        $usuario->nombre = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->documento = $data['documento'];
        $usuario->email = $data['email'];
        
        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }
        $usuario->save();

        // Actualizar cliente asociado para mantener consistencia
        $cliente = Cliente::where('email', $originalEmail)
            ->orWhere('documento', $originalDocumento)->first();
            
        if ($cliente) {
            $cliente->nombre = $data['nombre'];
            $cliente->apellido = $data['apellido'];
            $cliente->documento = $data['documento'];
            $cliente->email = $data['email'];
            $cliente->telefono = $data['telefono'] ?? $cliente->telefono;
            $cliente->direccion = $data['direccion'] ?? $cliente->direccion;
            $cliente->save();
        }

        Bitacora::registrar('UPDATE', 'usuarios', $usuario->id_usuario, 'Perfil actualizado');

        return redirect()->route('perfil')->with('success','Perfil actualizado');
    }

    /**
     * Actualiza la preferencia de tema visual (light/dark).
     * 
     * Se invoca vía AJAX/Fetch.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateTheme()
    {
        $u = Auth::user();
        request()->validate(['tema' => 'required|in:light,dark']);
        $u->tema = request('tema');
        $u->save();
        return response()->noContent();
    }
}
