<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\Producto;
use App\Models\Categoria;

class InventorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_decrementa_stock_por_id_y_codigo()
    {
        $cat = Categoria::create(['nombre'=>'Tmp','descripcion'=>'','estado'=>'activo']);
        $p1 = Producto::create([
            'codigo_barras' => 'ABC',
            'nombre' => 'P1',
            'id_categoria' => $cat->id_categoria,
            'precio_compra' => 1,
            'precio_venta' => 10,
            'stock' => 10,
            'estado' => 'activo',
        ]);
        $p2 = Producto::create([
            'codigo_barras' => 'XYZ',
            'nombre' => 'P2',
            'id_categoria' => $cat->id_categoria,
            'precio_compra' => 1,
            'precio_venta' => 10,
            'stock' => 5,
            'estado' => 'activo',
        ]);

        Http::fake([
            '*' => Http::response([
                ['id' => 'u1', 'id_producto' => $p1->id_producto, 'cantidad' => 3],
                ['id' => 'u2', 'codigo_barras' => 'XYZ', 'cantidad' => 10],
            ], 200)
        ]);

        $this->artisan('inventory:sync-usage --url=http://fake.local/usage')
            ->assertExitCode(0);

        $p1->refresh(); $p2->refresh();
        $this->assertEquals(7, $p1->stock);
        $this->assertEquals(0, $p2->stock);
    }
}
