<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Rol;

class ClienteHistorialTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role and user
        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        
        $this->usuario = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
            'estado' => 'activo',
            'documento' => '123456789'
        ]);

        // Create client
        $this->cliente = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Test',
            'documento' => '987654321',
            'telefono' => '555-5555',
            'email' => 'cliente@test.com',
            'direccion' => 'Calle Test 123',
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function puede_ver_historial_de_compras_del_cliente()
    {
        // Create 3 sales for this client manually
        $ventas = [];
        for ($i = 0; $i < 3; $i++) {
            $ventas[] = Venta::create([
                'id_cliente' => $this->cliente->id_cliente,
                'id_usuario' => $this->usuario->id_usuario,
                'fecha' => now(),
                'subtotal' => 100.00,
                'descuento' => 0.00,
                'impuesto' => 19.00,
                'total' => 119.00,
                'metodo_pago' => 'Efectivo',
                'estado' => 'completada'
            ]);
        }

        $response = $this->actingAs($this->usuario)->get(route('clientes.show', $this->cliente));

        $response->assertStatus(200);
        $response->assertSee('Historial de Compras');
        
        foreach ($ventas as $venta) {
            $response->assertSee('#' . $venta->id_venta);
            $response->assertSee(number_format($venta->total, 2));
        }
    }

    /** @test */
    public function muestra_mensaje_cuando_no_hay_compras()
    {
        $response = $this->actingAs($this->usuario)->get(route('clientes.show', $this->cliente));

        $response->assertStatus(200);
        $response->assertSee('Historial de Compras');
        $response->assertSee('Este cliente aún no ha realizado compras');
    }
}
