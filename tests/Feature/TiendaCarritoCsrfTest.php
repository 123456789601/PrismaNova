<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TiendaCarritoCsrfTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $clienteUser;
    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();

        $rolCliente = Rol::create(['nombre' => 'cliente']);

        $this->clienteUser = Usuario::create([
            'nombre' => 'Cliente',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'cliente@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCliente->id,
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Cat Test',
            'descripcion' => 'Desc',
            'estado' => 'activo',
        ]);

        $this->producto = Producto::create([
            'codigo_barras' => 'CB-TEST-1',
            'nombre' => 'Producto Test',
            'descripcion' => 'Desc',
            'imagen' => null,
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => null,
            'precio_compra' => 1,
            'precio_venta' => 2,
            'stock' => 10,
            'stock_minimo' => 1,
            'fecha_vencimiento' => null,
            'estado' => 'activo',
        ]);
    }

    public function test_api_productos_devuelve_json_paginado()
    {
        $response = $this->getJson('/api/productos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]);
    }

    public function test_cliente_puede_agregar_al_carrito_con_csrf_de_tienda()
    {
        $this->actingAs($this->clienteUser, 'web');

        $tienda = $this->get('/tienda');
        $tienda->assertStatus(200);

        preg_match('/<meta\\s+name="csrf-token"\\s+content="([^"]+)"/i', $tienda->getContent(), $m);
        $csrf = $m[1] ?? null;
        $this->assertNotEmpty($csrf);

        $add = $this->withHeaders([
            'X-CSRF-TOKEN' => $csrf,
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])->postJson('/tienda/carrito/agregar', [
            'id_producto' => $this->producto->id_producto,
        ]);

        $add->assertStatus(200)->assertJson([
            'ok' => true,
            'count' => 1,
        ]);
    }
}
