<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\VentaApiController;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tests\TestCase;

class ApiVentasReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_devuelve_resumen_y_datos_filtrados()
    {
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => 'ADM-1',
            'email' => 'admin@test.local',
            'password' => Hash::make('secret'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $this->actingAs($admin);

        $cliente = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Uno',
            'documento' => 'DOC-1',
            'telefono' => '123',
            'direccion' => 'Dir',
            'email' => 'cli@test.local',
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'General',
            'descripcion' => 'Cat',
            'estado' => 'activo',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto X',
            'id_categoria' => $categoria->id_categoria,
            'precio_compra' => 5,
            'precio_venta' => 10,
            'stock' => 100,
            'estado' => 'activo',
        ]);

        $metodo = MetodoPago::create([
            'nombre' => 'Tarjeta',
            'estado' => 'activo',
        ]);

        $venta = Venta::create([
            'id_cliente' => $cliente->id_cliente,
            'id_usuario' => $admin->id_usuario,
            'fecha' => now(),
            'subtotal' => 100,
            'descuento' => 10,
            'impuesto' => 0,
            'total' => 90,
            'metodo_pago' => 'tarjeta',
            'metodo_pago_id' => $metodo->id_metodo_pago,
            'estado' => 'completada',
        ]);

        DetalleVenta::create([
            'id_venta' => $venta->id_venta,
            'id_producto' => $producto->id_producto,
            'cantidad' => 10,
            'precio_unitario' => 10,
            'subtotal' => 100,
        ]);

        $controller = app(VentaApiController::class);
        $req = Request::create('/api/ventas','GET', [
            'estado' => 'completada',
            'cliente' => 'Cliente',
        ]);
        $req->setUserResolver(fn() => $admin);
        $res = $controller->index($req);
        $json = $res->getData(true);

        $this->assertArrayHasKey('resumen', $json);
        $this->assertEquals(90.0, $json['resumen']['total']);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('data', $json['data']);
        $this->assertNotEmpty($json['data']['data']);
        $row = $json['data']['data'][0];
        $this->assertEquals($venta->id_venta, $row['id_venta']);
        $this->assertEquals('completada', $row['estado']);
    }
}

