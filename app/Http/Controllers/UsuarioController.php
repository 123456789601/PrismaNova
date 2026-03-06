<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Support\Facades\Hash;

use App\Models\Bitacora;

/**
 * Class UsuarioController
 * 
 * Controlador para la gestión administrativa de usuarios del sistema.
 * Permite listar, crear, editar y eliminar usuarios (empleados/admins).
 */
class UsuarioController extends Controller
{
    /**
     * Muestra el listado de usuarios registrados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $usuarios = Usuario::orderBy('id_usuario','desc')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Muestra los detalles de un usuario específico.
     *
     * @param Usuario $usuario
     * @return \Illuminate\View\View
     */
    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * 
     * Encripta la contraseña antes de guardar.
     *
     * @param StoreUsuarioRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $usuario = Usuario::create($data);
        Bitacora::registrar('CREATE', 'usuarios', $usuario->id_usuario, 'Usuario creado: ' . $usuario->email);
        return redirect()->route('usuarios.index')->with('success','Usuario creado');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param Usuario $usuario
     * @return \Illuminate\View\View
     */
    public function edit(Usuario $usuario)
    {
        $roles = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualiza la información de un usuario.
     * 
     * Gestiona la actualización de contraseña solo si se proporciona una nueva.
     *
     * @param UpdateUsuarioRequest $request
     * @param Usuario $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        $data = $request->validated();
        
        // Solo actualizar password si el campo no está vacío
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $usuario->update($data);
        Bitacora::registrar('UPDATE', 'usuarios', $usuario->id_usuario, 'Usuario actualizado');
        return redirect()->route('usuarios.index')->with('success','Usuario actualizado');
    }

    /**
     * Elimina un usuario del sistema.
     *
     * @param Usuario $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Usuario $usuario)
    {
        try {
            $id = $usuario->id_usuario;
            $usuario->delete();
            Bitacora::registrar('DELETE', 'usuarios', $id, 'Usuario eliminado');
            return redirect()->route('usuarios.index')->with('success','Usuario eliminado');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error','No se puede eliminar el usuario porque tiene registros asociados (ventas, compras, etc).');
        }
    }
}
