<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Cliente;

class ClienteStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup roles and admin user
        $rolAdmin = Rol::create(['nombre' => 'admin']);
        $this->admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '123456789',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolAdmin->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function can_create_client_with_formatted_document()
    {
        // Simulate input "12.345.678-9" which is common in many countries
        $response = $this->actingAs($this->admin)
            ->post(route('clientes.store'), [
                'nombre' => 'Cliente',
                'apellido' => 'Format',
                'documento' => '12.345.678-9', // Should be cleaned to 123456789
                'telefono' => '3001234567',
                'estado' => 'activo',
                'email' => 'format@test.com'
            ]);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('clientes', [
            'documento' => '123456789',
            'nombre' => 'Cliente',
            'apellido' => 'Format'
        ]);
    }

    /** @test */
    public function can_create_client_with_formatted_phone()
    {
        // Simulate input with spaces, parenthesis and dashes
        $response = $this->actingAs($this->admin)
            ->post(route('clientes.store'), [
                'nombre' => 'Cliente',
                'apellido' => 'Phone',
                'documento' => '987654321',
                'telefono' => '+57 (300) 123-4567', // Should be cleaned to +573001234567
                'estado' => 'activo',
                'email' => 'phone@test.com'
            ]);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('clientes', [
            'telefono' => '+573001234567',
            'documento' => '987654321'
        ]);
    }

    /** @test */
    public function validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('clientes.store'), [
                'nombre' => '',
                'documento' => '',
            ]);

        $response->assertSessionHasErrors(['nombre', 'documento']);
    }
}
