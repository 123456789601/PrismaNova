<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Rol;

class MovimientosCajaDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_estado_caja_returns_movements()
    {
        // 1. Setup: Create admin user and open cash register
        $adminRole = Rol::create(['nombre' => 'admin']);
        $user = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'documento' => '12345678',
            'rol_id' => $adminRole->id, // Fixed: use id instead of id_rol
            'estado' => 'activo'
        ]);

        $this->actingAs($user);

        // Open Caja
        $caja = Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 100,
            'estado' => 'abierta',
        ]);

        // 2. Action: Create movements
        MovimientoCaja::create([
            'id_caja' => $caja->id_caja,
            'tipo' => 'ingreso',
            'monto' => 50,
            'descripcion' => 'Ingreso Test',
            'fecha' => now()
        ]);

        MovimientoCaja::create([
            'id_caja' => $caja->id_caja,
            'tipo' => 'egreso',
            'monto' => 20,
            'descripcion' => 'Egreso Test',
            'fecha' => now()
        ]);

        // 3. Request: Get caja status
        $response = $this->getJson(route('caja.estado'));

        // 4. Assert: Check structure and data
        $response->assertStatus(200)
            ->assertJsonStructure([
                'abierta',
                'movimientos' => [
                    '*' => [
                        'tipo',
                        'monto',
                        'descripcion',
                        'hora'
                    ]
                ]
            ]);

        $movements = $response->json('movimientos');
        $this->assertCount(2, $movements);
        
        // Check order (latest first)
        $this->assertEquals('Egreso Test', $movements[0]['descripcion']);
        $this->assertEquals('Ingreso Test', $movements[1]['descripcion']);
    }
}
