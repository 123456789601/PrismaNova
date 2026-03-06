<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\MensajeContacto;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        if (Rol::count() === 0) {
            Rol::create(['nombre' => 'admin']);
            Rol::create(['nombre' => 'cajero']);
            Rol::create(['nombre' => 'cliente']);
        }
    }

    /** @test */
    public function contact_form_saves_message_to_database()
    {
        $response = $this->post(route('contact.send'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ]);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('mensaje_contactos', [
            'nombre' => 'John Doe',
            'email' => 'john@example.com',
            'mensaje' => 'This is a test message.',
            'leido' => false
        ]);
    }

    /** @test */
    public function admin_can_view_messages()
    {
        $adminRole = Rol::where('nombre', 'admin')->first();
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $adminRole->id,
            'estado' => 'activo',
            'documento' => '1234567890'
        ]);

        MensajeContacto::create([
            'nombre' => 'Tester',
            'email' => 'tester@test.com',
            'mensaje' => 'Message content'
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.mensajes.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Tester');
        $response->assertSee('Message content');
    }

    /** @test */
    public function non_admin_cannot_view_messages()
    {
        $cajeroRole = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'User',
            'email' => 'cajero@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $cajeroRole->id,
            'estado' => 'activo',
            'documento' => '0987654321'
        ]);

        $this->actingAs($cajero);

        $response = $this->get(route('admin.mensajes.index'));
        
        $response->assertStatus(403); // Or whatever your middleware returns for unauthorized
    }
}
