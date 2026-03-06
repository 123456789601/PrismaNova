<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Rol;

class RegistrationDuplicateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear rol cliente
        Rol::create(['nombre' => 'cliente', 'descripcion' => 'Cliente Final']);
    }

    /** @test */
    public function permite_registro_si_email_existe_en_clientes_pero_no_en_usuarios()
    {
        // 1. Crear un cliente existente (ej. vino a la tienda física)
        $email = 'cliente.existente@test.com';
        $documento = '987654321';
        
        Cliente::create([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'documento' => $documento,
            'email' => $email,
            'estado' => 'activo'
        ]);

        // Verificar que el cliente existe
        $this->assertDatabaseHas('clientes', ['email' => $email]);
        // Verificar que NO existe usuario
        $this->assertDatabaseMissing('usuarios', ['email' => $email]);

        // 2. Intentar registrarse como usuario con el mismo email
        $response = $this->post(route('register.attempt'), [
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'documento' => $documento, // Mismo documento
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // 3. Debería ser exitoso (redirección a dashboard)
        $response->assertRedirect(route('dashboard'));
        
        // 4. Verificar que se creó el usuario
        $this->assertDatabaseHas('usuarios', ['email' => $email]);
        
        // 5. Verificar que NO se duplicó el cliente (sigue habiendo 1)
        $this->assertEquals(1, Cliente::where('email', $email)->count());
    }

    /** @test */
    public function actualiza_email_cliente_si_coincide_documento_pero_email_diferente()
    {
        // Caso: Cliente registrado presencialmente con email antiguo o nulo, 
        // ahora se registra web con email nuevo pero mismo documento.
        
        $documento = '123456789';
        $emailAntiguo = 'antiguo@test.com';
        $emailNuevo = 'nuevo@test.com';
        
        Cliente::create([
            'nombre' => 'Maria',
            'apellido' => 'Gomez',
            'documento' => $documento,
            'email' => $emailAntiguo,
            'estado' => 'activo'
        ]);

        $response = $this->post(route('register.attempt'), [
            'nombre' => 'Maria',
            'apellido' => 'Gomez',
            'documento' => $documento,
            'email' => $emailNuevo,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        
        // Usuario creado con email nuevo
        $this->assertDatabaseHas('usuarios', ['email' => $emailNuevo]);
        
        // Cliente actualizado con email nuevo (la lógica que implementamos)
        $this->assertDatabaseHas('clientes', [
            'documento' => $documento,
            'email' => $emailNuevo
        ]);
        
        // No debe haber cliente con email antiguo
        $this->assertDatabaseMissing('clientes', ['email' => $emailAntiguo]);
    }
}
