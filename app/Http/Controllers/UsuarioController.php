<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Support\Facades\Hash;

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
        return view('usuarios.create');
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
        Usuario::create($data);
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
        return view('usuarios.edit', compact('usuario'));
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
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success','Usuario eliminado');
    }
}
