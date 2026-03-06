<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\MensajeContacto;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Save to database
            MensajeContacto::create([
                'nombre' => $data['name'],
                'email' => $data['email'],
                'mensaje' => $data['message'],
                'leido' => false
            ]);

            // Try to send email to admin, but don't fail if it doesn't work (e.g. local env)
            try {
                Mail::raw("Nuevo mensaje de contacto:\n\nNombre: {$data['name']}\nEmail: {$data['email']}\n\nMensaje:\n{$data['message']}", function ($message) use ($data) {
                    $message->to('alejandroaris12300@gmail.com')
                            ->subject('Nuevo mensaje de contacto - PrismaNova');
                });
            } catch (\Exception $e) {
                // Log email error but continue success flow since DB save worked
                \Illuminate\Support\Facades\Log::error('Error enviando email de contacto: ' . $e->getMessage());
            }
            
            return back()->with('success', 'Mensaje enviado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el mensaje: ' . $e->getMessage());
        }
    }
    
    public function adminIndex()
    {
        $mensajes = MensajeContacto::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.mensajes.index', compact('mensajes'));
    }
}
