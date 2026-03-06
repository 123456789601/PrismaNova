<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\MetodoPago;
use App\Models\MovimientoCaja;

class PosFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $cliente;

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

        $this->cliente = \App\Models\Cliente::create([
            'nombre' => 'Test Client',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'test@client.com',
            'telefono' => '123456789',
            'direccion' => 'Test Address',
            'estado' => 'activo'
        ]);
    }

    public function test_recent_sales_endpoint()
    {
        // Create some sales
        Venta::create([
            'id_usuario' => $this->admin->id_usuario,
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now(),
            'subtotal' => 100,
            'descuento' => 0,
            'impuesto' => 18,
            'total' => 118,
            'estado' => 'completada'
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('ventas.recent'));

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function test_cash_register_status_endpoint()
    {
        // Open a box
        $caja = Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 100,
            'estado' => 'abierta'
        ]);

        // Add some sales
        Venta::create([
            'id_usuario' => $this->admin->id_usuario,
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now()->addMinute(),
            'total' => 50,
            'metodo_pago' => 'Efectivo',
            'estado' => 'completada'
        ]);

        Venta::create([
            'id_usuario' => $this->admin->id_usuario,
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now()->addMinutes(2),
            'total' => 70,
            'metodo_pago' => 'Tarjeta',
            'estado' => 'completada'
        ]);

        // Add manual movement
        MovimientoCaja::create([
            'id_caja' => $caja->id_caja,
            'tipo' => 'ingreso',
            'monto' => 20,
            'descripcion' => 'Test',
            'fecha' => now()
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('caja.estado'));

        $response->assertStatus(200)
                 ->assertJson([
                     'abierta' => true,
                     'monto_inicial' => 100,
                     'ventas_efectivo' => 50,
                     'ventas_tarjeta' => 70,
                     'ingresos' => 20,
                     'saldo_esperado' => 170 // 100 + 50 + 20
                 ]);
    }

    public function test_json_movement_registration()
    {
        $caja = Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 100,
            'estado' => 'abierta'
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('caja.movimiento.store', $caja->id_caja), [
            'tipo' => 'egreso',
            'monto' => 50,
            'descripcion' => 'Pago proveedor'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Movimiento registrado correctamente'
                 ]);

        $this->assertDatabaseHas('movimientos_caja', [
            'id_caja' => $caja->id_caja,
            'tipo' => 'egreso',
            'monto' => 50,
            'descripcion' => 'Pago proveedor'
        ]);
    }

    public function test_json_void_sale()
    {
        $proveedor = \App\Models\Proveedor::create([
            'nombre_empresa' => 'Test Provider',
            'contacto' => 'Test Contact',
            'telefono' => '123456789',
            'email' => 'provider@test.com',
            'direccion' => 'Test Address',
            'estado' => 'activo'
        ]);

        $categoria = \App\Models\Categoria::create([
            'nombre' => 'Test Category',
            'descripcion' => 'Test Description',
            'estado' => 'activo'
        ]);

        // Create a product to test stock reversal
        $producto = \App\Models\Producto::create([
            'nombre' => 'Test Product',
            'codigo' => 'TEST001',
            'precio_venta' => 100,
            'precio_compra' => 80,
            'stock' => 10,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor
        ]);

        $venta = Venta::create([
            'id_usuario' => $this->admin->id_usuario,
            'id_cliente' => $this->cliente->id_cliente,
            'fecha' => now(),
            'subtotal' => 100,
            'descuento' => 0,
            'impuesto' => 18,
            'total' => 118,
            'estado' => 'completada'
        ]);

        \App\Models\DetalleVenta::create([
            'id_venta' => $venta->id_venta,
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
            'precio_unitario' => 100,
            'subtotal' => 200
        ]);

        // Simulate stock reduction
        $producto->decrement('stock', 2); // Stock becomes 8

        $response = $this->actingAs($this->admin)->patchJson(route('ventas.anular', $venta->id_venta));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Venta anulada correctamente'
                 ]);
        
        $this->assertDatabaseHas('ventas', [
            'id_venta' => $venta->id_venta,
            'estado' => 'anulada'
        ]);

        // Check stock restored
        $this->assertEquals(10, $producto->fresh()->stock);
    }

    public function test_suspend_and_resume_sale_api()
    {
        // 1. Suspend Sale
        $cartContent = [
            ['id' => 1, 'name' => 'Prod A', 'qty' => 2, 'price' => 50]
        ];

        $response = $this->actingAs($this->admin)->postJson(route('ventas.suspendidas.store'), [
            'contenido' => $cartContent,
            'total' => 100,
            'id_cliente' => $this->cliente->id_cliente,
            'nota' => 'Mesa 5'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('ventas_suspendidas', [
            'nota' => 'Mesa 5',
            'total' => 100,
            'id_usuario' => $this->admin->id_usuario
        ]);

        $suspendedId = $response->json('venta.id_venta_suspendida');

        // 2. List Suspended Sales
        $responseList = $this->actingAs($this->admin)->getJson(route('ventas.suspendidas.index'));
        
        $responseList->assertStatus(200)
                     ->assertJsonFragment(['nota' => 'Mesa 5']);

        // 3. Delete/Resume Suspended Sale
        $responseDelete = $this->actingAs($this->admin)->deleteJson(route('ventas.suspendidas.destroy', $suspendedId));
        
        $responseDelete->assertStatus(200)
                       ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('ventas_suspendidas', [
            'id_venta_suspendida' => $suspendedId
        ]);
    }
}
