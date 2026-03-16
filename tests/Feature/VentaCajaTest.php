<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\MetodoPago;

class VentaCajaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        if (Rol::count() === 0) {
            Rol::create(['nombre' => 'admin']);
            Rol::create(['nombre' => 'cajero']);
        }

        // Share configuracion for views
        \Illuminate\Support\Facades\View::share('configuracion', [
            'moneda' => '$',
            'impuesto' => 19,
            'nombre_tienda' => 'PrismaNova Test'
        ]);
    }

    /** @test */
    public function cannot_access_pos_without_open_cashbox()
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

        // Ensure no open cashbox
        Caja::query()->delete();
        
        $this->assertEquals(0, Caja::where('estado', 'abierta')->count());

        $response = $this->get(route('ventas.pos'));
        
        $response->assertRedirect(route('caja.index'));
        $response->assertSessionHas('error', 'Debe abrir una caja para realizar ventas.');
    }

    /** @test */
    public function cannot_create_sale_without_open_cashbox()
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

        // Ensure no open cashbox
        Caja::query()->delete();

        // Create dependencies for sale
        $cliente = Cliente::create([
            'nombre' => 'Cliente Test',
            'apellido' => 'Apellido Test',
            'documento' => '111222333',
            'email' => 'cliente@test.com',
            'telefono' => '3001234567'
        ]);
        
        $proveedor = Proveedor::create(['nombre_empresa' => 'Prov', 'contacto' => 'C', 'telefono' => '1', 'email' => 'p@p.com']);
        $categoria = Categoria::create(['nombre' => 'Cat', 'descripcion' => 'Desc']);
        
        $producto = Producto::create([
            'codigo_barras' => 'PROD001',
            'nombre' => 'Producto Test',
            'descripcion' => 'Desc',
            'precio_compra' => 50,
            'precio_venta' => 100,
            'stock' => 10,
            'stock_minimo' => 5,
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor,
            'estado' => 'activo'
        ]);

        $response = $this->post(route('ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'id_producto' => [$producto->id_producto],
            'cantidad' => [1],
            'precio_unitario' => [100],
            'metodo_pago' => 'Efectivo'
        ]);

        $response->assertRedirect(route('caja.index'));
        $response->assertSessionHas('error', 'Debe abrir una caja para realizar ventas.');
        
        // Assert sale was NOT created
        $this->assertDatabaseCount('ventas', 0);
    }

    /** @test */
    public function can_access_pos_with_open_cashbox()
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

        // Open cashbox
        Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 1000,
            'estado' => 'abierta'
        ]);

        $response = $this->get(route('ventas.pos'));
        $response->assertStatus(200);
    }

    /** @test */
    public function requiere_referencia_en_transferencia_desde_cajero_o_admin()
    {
        $rolCajero = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'cajero2@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCajero->id,
            'estado' => 'activo'
        ]);
        $this->actingAs($cajero);

        Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 1000,
            'estado' => 'abierta'
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Cliente Test',
            'apellido' => 'Apellido Test',
            'documento' => '111222334',
            'email' => 'cliente2@test.com',
            'telefono' => '3001234567'
        ]);

        $proveedor = Proveedor::create(['nombre_empresa' => 'Prov', 'contacto' => 'C', 'telefono' => '1', 'email' => 'p2@p.com']);
        $categoria = Categoria::create(['nombre' => 'Cat2', 'descripcion' => 'Desc']);
        $producto = Producto::create([
            'codigo_barras' => 'PROD002',
            'nombre' => 'Producto Test',
            'descripcion' => 'Desc',
            'precio_compra' => 50,
            'precio_venta' => 100,
            'stock' => 10,
            'stock_minimo' => 5,
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor,
            'estado' => 'activo'
        ]);

        $mp = MetodoPago::create(['nombre' => 'Transferencia', 'estado' => 'activo']);

        $response = $this->post(route('ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'metodo_pago_id' => $mp->id_metodo_pago,
            'metodo_pago' => 'Transferencia',
            'id_producto' => [$producto->id_producto],
            'cantidad' => [1],
            'precio_unitario' => [100],
        ]);

        $response->assertSessionHasErrors(['referencia_pago']);
        $this->assertDatabaseCount('ventas', 0);
    }

    /** @test */
    public function requiere_referencia_y_ultimos_4_en_tarjeta_desde_cajero_o_admin()
    {
        $rolCajero = Rol::where('nombre', 'cajero')->first();
        $cajero = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'cajero3@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolCajero->id,
            'estado' => 'activo'
        ]);
        $this->actingAs($cajero);

        Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 1000,
            'estado' => 'abierta'
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Cliente Test',
            'apellido' => 'Apellido Test',
            'documento' => '111222335',
            'email' => 'cliente3@test.com',
            'telefono' => '3001234567'
        ]);

        $proveedor = Proveedor::create(['nombre_empresa' => 'Prov', 'contacto' => 'C', 'telefono' => '1', 'email' => 'p3@p.com']);
        $categoria = Categoria::create(['nombre' => 'Cat3', 'descripcion' => 'Desc']);
        $producto = Producto::create([
            'codigo_barras' => 'PROD003',
            'nombre' => 'Producto Test',
            'descripcion' => 'Desc',
            'precio_compra' => 50,
            'precio_venta' => 100,
            'stock' => 10,
            'stock_minimo' => 5,
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor,
            'estado' => 'activo'
        ]);

        $mp = MetodoPago::create(['nombre' => 'Tarjeta', 'estado' => 'activo']);

        $response = $this->post(route('ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'metodo_pago_id' => $mp->id_metodo_pago,
            'metodo_pago' => 'Tarjeta',
            'id_producto' => [$producto->id_producto],
            'cantidad' => [1],
            'precio_unitario' => [100],
            'referencia_pago' => 'REF-1',
        ]);

        $response->assertSessionHasErrors(['ultimos_digitos']);
        $this->assertDatabaseCount('ventas', 0);
    }
}
