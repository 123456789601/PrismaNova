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

class ApiVentasExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_csv_devuelve_contenido_esperado()
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
            'apellido' => 'Dos',
            'documento' => 'DOC-2',
            'telefono' => '123',
            'direccion' => 'Dir',
            'email' => 'cli2@test.local',
            'estado' => 'activo',
        ]);

        $categoria = Categoria::create([
            'nombre' => 'General',
            'descripcion' => 'Cat',
            'estado' => 'activo',
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto Y',
            'id_categoria' => $categoria->id_categoria,
            'precio_compra' => 5,
            'precio_venta' => 10,
            'stock' => 100,
            'estado' => 'activo',
        ]);

        $metodo = MetodoPago::create([
            'nombre' => 'Efectivo',
            'estado' => 'activo',
        ]);

        $venta = Venta::create([
            'id_cliente' => $cliente->id_cliente,
            'id_usuario' => $admin->id_usuario,
            'fecha' => now(),
            'subtotal' => 50,
            'descuento' => 5,
            'impuesto' => 0,
            'total' => 45,
            'metodo_pago' => 'efectivo',
            'metodo_pago_id' => $metodo->id_metodo_pago,
            'estado' => 'completada',
        ]);

        DetalleVenta::create([
            'id_venta' => $venta->id_venta,
            'id_producto' => $producto->id_producto,
            'cantidad' => 3,
            'precio_unitario' => 15,
            'subtotal' => 45,
        ]);

        $controller = app(VentaApiController::class);
        $req = Request::create('/api/ventas/export','GET', [
            'estado' => 'completada',
            'cliente' => 'Cliente',
        ]);
        $req->setUserResolver(fn() => $admin);

        $response = $controller->exportCsv($req);
        ob_start();
        $response->sendContent();
        $csv = ob_get_clean();

        $this->assertIsString($csv);
        $this->assertStringContainsString('ID,Fecha,Cliente,Total,Estado', $csv);
        $this->assertStringContainsString('Cantidad total', $csv);
        $this->assertStringContainsString('Descuento', $csv);
        $this->assertStringContainsString((string) $venta->id_venta, $csv);
        $this->assertStringContainsString('Cliente', $csv);
        $this->assertStringContainsString('45.00', $csv);
        $this->assertStringContainsString('completada', $csv);
        $this->assertStringContainsString('Efectivo', $csv);
        $this->assertStringContainsString('3', $csv);
        $this->assertStringContainsString('5.00', $csv);
    }
}
