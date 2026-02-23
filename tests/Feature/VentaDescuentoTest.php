<?php
namespace Tests\Feature;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VentaDescuentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_aplica_descuento_automatico_por_tier_de_subtotal_y_por_cantidad()
    {
        $user = Usuario::create([
            'nombre' => 'Cajero',
            'apellido' => 'Test',
            'documento' => 'CAJ-DESC-1',
            'email' => 'cajero.desc@test.local',
            'password' => Hash::make('secret'),
            'rol' => 'cajero',
            'estado' => 'activo',
        ]);
        $this->actingAs($user);

        $cat = Categoria::create([
            'nombre' => 'General',
            'descripcion' => null,
            'estado' => 'activo',
        ]);
        $p = Producto::create([
            'codigo_barras' => 'X123',
            'nombre' => 'Producto X',
            'descripcion' => null,
            'id_categoria' => $cat->id_categoria,
            'id_proveedor' => null,
            'precio_compra' => 80,
            'precio_venta' => 120,
            'stock' => 100,
            'stock_minimo' => 1,
            'fecha_vencimiento' => null,
            'estado' => 'activo',
        ]);

        $payload = [
            'items' => [
                ['id_producto' => $p->id_producto, 'cantidad' => 2], // subtotal 240 => 10% = 24
            ],
            'metodo_pago' => 'efectivo',
            'id_cliente' => null,
        ];

        $items = [['precio' => 120, 'cantidad' => 2]];
        $tot = \App\Http\Controllers\Api\VentaApiController::computeTotals($items);
        $this->assertEquals(240.0, (float)$tot['subtotal']);
        $this->assertEquals(24.0, (float)$tot['descuento']);
        $this->assertEquals(216.0, (float)$tot['total']);
    }
}
