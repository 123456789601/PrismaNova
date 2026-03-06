<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;

class CajeroMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist
        if (Rol::count() === 0) {
            Rol::create(['nombre' => 'admin']);
            Rol::create(['nombre' => 'cajero']);
            Rol::create(['nombre' => 'bodeguero']);
            Rol::create(['nombre' => 'cliente']);
        }
        
        // Share configuracion for views
        \Illuminate\Support\Facades\View::share('configuracion', [
            'moneda' => '$',
            'impuesto' => 19,
            'nombre_tienda' => 'PrismaNova Test'
        ]);
    }

    /** @test */
    public function cajero_cannot_access_unauthorized_routes()
    {
        $rolCajero = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'cajero@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCajero->id,
            'estado' => 'activo'
        ]);

        $this->actingAs($cajero);

        // Should NOT be able to access Productos
        $response = $this->get(route('productos.index'));
        $response->assertStatus(403); // Forbidden

        // Should NOT be able to access Compras
        $response = $this->get(route('compras.index'));
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function cajero_can_access_authorized_routes()
    {
        $rolCajero = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero2',
            'apellido' => 'Test2',
            'documento' => '87654321',
            'email' => 'cajero2@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCajero->id,
            'estado' => 'activo'
        ]);

        $this->actingAs($cajero);

        // Should be able to access Clientes
        $response = $this->get(route('clientes.index'));
        $response->assertStatus(200);

        // Should be able to access Ventas
        $response = $this->get(route('ventas.index'));
        $response->assertStatus(200);

        // Should be able to access Caja
        $response = $this->get(route('caja.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_links_are_filtered_for_cajero()
    {
        // Mock Cache to avoid SQL errors in DashboardController with SQLite (HOUR function)
        \Illuminate\Support\Facades\Cache::shouldReceive('remember')
            ->andReturn([
                'ventas_hoy_total' => 100,
                'ventas_hoy_count' => 5,
                'caja_abierta' => true,
                'ventas_hoy_usuario_count' => 2,
                'stock_bajo' => 0,
                'productos_stock_bajo' => collect(),
                // Add other necessary keys if needed to avoid undefined index
            ]);

        $rolCajero = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero3',
            'apellido' => 'Test3',
            'documento' => '11223344',
            'email' => 'cajero3@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCajero->id,
            'estado' => 'activo'
        ]);

        $this->actingAs($cajero);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        // Assert Correct Links are Present
        $response->assertSee(route('clientes.index'));
        $response->assertSee(route('ventas.pos'));
        $response->assertSee(route('ventas.index'));
        $response->assertSee(route('caja.index'));

        // Assert Incorrect Links are ABSENT
        $response->assertDontSee(route('productos.index'));
        $response->assertDontSee(route('compras.index'));
    }
}
