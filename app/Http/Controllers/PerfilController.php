<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function show()
    {
        $usuario = Auth::user();
        $cliente = Cliente::where('email', $usuario->email)->orWhere('documento', $usuario->documento)->first();
        return view('perfil.index', compact('usuario','cliente'));
    }

    public function update(UpdatePerfilRequest $request)
    {
        $usuario = Auth::user();
        $data = $request->validated();
        $usuario->nombre = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->documento = $data['documento'];
        $usuario->email = $data['email'];
        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }
        $usuario->save();

        $cliente = Cliente::where('email', $usuario->getOriginal('email'))
            ->orWhere('documento', $usuario->getOriginal('documento'))->first();
        if ($cliente) {
            $cliente->nombre = $data['nombre'];
            $cliente->apellido = $data['apellido'];
            $cliente->documento = $data['documento'];
            $cliente->email = $data['email'];
            $cliente->telefono = $data['telefono'] ?? $cliente->telefono;
            $cliente->direccion = $data['direccion'] ?? $cliente->direccion;
            $cliente->save();
        }

        return redirect()->route('perfil')->with('success','Perfil actualizado');
    }
}
