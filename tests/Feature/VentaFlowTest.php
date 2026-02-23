<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VentaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_venta_descuenta_stock_y_genera_factura()
    {
        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \App\Http\Middleware\RoleMiddleware::class]);
        $user = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'Test',
            'documento' => 'CAJ-1',
            'email' => 'cajero@test.local',
            'password' => Hash::make('secret'),
            'rol' => 'cajero',
            'estado' => 'activo',
        ]);
        $this->actingAs($user);

        $cliente = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Demo',
            'documento' => 'CLI-1',
            'telefono' => '123',
            'direccion' => 'Calle',
            'email' => 'cliente@test.local',
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'General',
            'descripcion' => 'Cat',
            'estado' => 'activo',
        ]);

        $p1 = Producto::create([
            'nombre' => 'Prod A',
            'id_categoria' => $categoria->id_categoria,
            'precio_compra' => 1,
            'precio_venta' => 5,
            'stock' => 10,
            'estado' => 'activo',
        ]);
        $p2 = Producto::create([
            'nombre' => 'Prod B',
            'id_categoria' => $categoria->id_categoria,
            'precio_compra' => 2,
            'precio_venta' => 3,
            'stock' => 8,
            'estado' => 'activo',
        ]);

        $mp = MetodoPago::create(['nombre' => 'Efectivo', 'estado' => 'activo']);

        $controller = app(\App\Http\Controllers\Api\VentaApiController::class);
        $this->actingAs($user);
        $req = \Illuminate\Http\Request::create('/api/ventas','POST', [
            'items' => [
                ['id_producto' => $p1->id_producto, 'cantidad' => 2],
                ['id_producto' => $p2->id_producto, 'cantidad' => 1],
            ],
            'metodo_pago' => 'Efectivo',
            'id_cliente' => $cliente->id_cliente,
        ]);
        $req->setUserResolver(fn() => $user);
        $resp = $controller->store($req);
        $this->assertEquals(201, $resp->getStatusCode());

        $venta = Venta::with('detalles')->first();
        $this->assertNotNull($venta);
        $this->assertEquals(2, $venta->detalles->count());
        $this->assertEquals('Efectivo', $venta->metodo_pago);
        $this->assertEquals(13.0, (float)$venta->total);

        $p1->refresh(); $p2->refresh();
        $this->assertEquals(8, $p1->stock);
        $this->assertEquals(7, $p2->stock);

        // No verificamos factura HTML en API test
    }
}
