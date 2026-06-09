<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\MensajeContacto;

/**
 * Class ContactController
 * 
 * Gestiona el formulario de contacto y los mensajes recibidos.
 * Permite a usuarios enviar mensajes y a administradores verlos.
 */
class ContactController extends Controller
{
    /**
     * Muestra el formulario de contacto público.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Procesa el envío del mensaje de contacto.
     * 
     * Guarda el mensaje en la base de datos y envía una notificación por correo
     * al administrador. El envío de correo es opcional (no falla si hay error).
     *
     * @param  \Illuminate\Http\Request  $request Solicitud con nombre, email y mensaje.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error.
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Guardar en base de datos
            MensajeContacto::create([
                'nombre' => $data['name'],
                'email' => $data['email'],
                'mensaje' => $data['message'],
                'leido' => false
            ]);

            // Intentar enviar correo al administrador, pero no fallar si no funciona (ej. entorno local)
            try {
                Mail::raw("Nuevo mensaje de contacto:\n\nNombre: {$data['name']}\nEmail: {$data['email']}\n\nMensaje:\n{$data['message']}", function ($message) use ($data) {
                    $message->to('alejandroaris12300@gmail.com')
                            ->subject('Nuevo mensaje de contacto - PrismaNova');
                });
            } catch (\Exception $e) {
                // Registrar error de correo pero continuar con flujo de éxito ya que el guardado en BD funcionó
                \Illuminate\Support\Facades\Log::error('Error enviando email de contacto: ' . $e->getMessage());
            }
            
            return back()->with('success', 'Mensaje enviado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el mensaje: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el listado de mensajes de contacto para administradores.
     *
     * @return \Illuminate\View\View Vista con los mensajes recibidos.
     */
    public function adminIndex()
    {
        $mensajes = MensajeContacto::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.mensajes.index', compact('mensajes'));
    }
}
