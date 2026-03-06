<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $cliente;
    protected $producto;
    protected $metodoEfectivo;
    protected $metodoTarjeta;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol' => 'admin',
            'estado' => 'activo'
        ]);
        
        $this->cliente = Cliente::create([
            'nombre' => 'Test Client',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'test@client.com',
            'telefono' => '123456789',
            'direccion' => 'Test Address',
            'estado' => 'activo'
        ]);
        
        // Create Category first
        $categoria = \App\Models\Categoria::create([
            'nombre' => 'General',
            'descripcion' => 'General Category',
            'estado' => 'activo'
        ]);

        $this->producto = Producto::create([
            'codigo_barras' => 'PROD001',
            'nombre' => 'Producto Test',
            'descripcion' => 'Desc',
            'precio' => 100.00, // This is 'precio_venta' in some models? Let's check
            'costo' => 50.00,
            'stock' => 50,
            'stock_minimo' => 5,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria
        ]);

        $this->metodoEfectivo = MetodoPago::create(['nombre' => 'Efectivo', 'estado' => 'activo']);
        $this->metodoTarjeta = MetodoPago::create(['nombre' => 'Tarjeta', 'estado' => 'activo']);
    }

    public function test_pos_page_loads()
    {
        $response = $this->actingAs($this->admin)->get(route('ventas.pos'));
        $response->assertStatus(200);
        $response->assertSee('Punto de Venta');
    }

    public function test_pos_sale_cash_with_change()
    {
        $response = $this->actingAs($this->admin)->post(route('ventas.store'), [
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'metodo_pago_id' => $this->metodoEfectivo->id_metodo_pago,
            'metodo_pago' => 'Efectivo',
            'id_producto' => [$this->producto->id_producto],
            'cantidad' => [2], // 2 * 100 = 200
            'precio_unitario' => [100.00],
            'monto_recibido' => 250.00,
            'impuesto' => 0
        ]);

        $response->assertStatus(302); // Redirect back or to invoice
        
        $venta = Venta::latest()->first();
        // Subtotal 200 -> Discount 10% (>=200) = 20 -> Total 180
        $this->assertEquals(180.00, $venta->total);
        $this->assertEquals(250.00, $venta->monto_recibido);
        $this->assertEquals(70.00, $venta->cambio);
        $this->assertEquals('Efectivo', $venta->metodo_pago);
    }

    public function test_pos_sale_card_with_reference()
    {
        $response = $this->actingAs($this->admin)->post(route('ventas.store'), [
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'metodo_pago_id' => $this->metodoTarjeta->id_metodo_pago,
            'metodo_pago' => 'Tarjeta',
            'id_producto' => [$this->producto->id_producto],
            'cantidad' => [1],
            'precio_unitario' => [100.00],
            'referencia_pago' => 'REF123456',
            'ultimos_digitos' => '4242',
            'impuesto' => 0
        ]);

        $venta = Venta::latest()->first();
        // Subtotal 100 -> Discount 5% (>=100) = 5 -> Total 95
        $this->assertEquals(95.00, $venta->total);
        $this->assertEquals('REF123456', $venta->referencia_pago);
        $this->assertEquals('4242', $venta->ultimos_digitos);
    }

    public function test_pos_quick_client_creation()
    {
        $response = $this->actingAs($this->admin)->postJson(route('clientes.store'), [
            'nombre' => 'Quick',
            'apellido' => 'Client',
            'documento' => '87654321',
            'email' => 'quick@test.com',
            'telefono' => '987654321',
            'direccion' => 'Quick Address',
            'estado' => 'activo'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Cliente creado con éxito'
                 ]);

        $this->assertDatabaseHas('clientes', [
            'documento' => '87654321',
            'email' => 'quick@test.com'
        ]);
    }
}
