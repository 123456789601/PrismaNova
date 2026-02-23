<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiProductosTest extends TestCase
{
    use RefreshDatabase;

    public function test_listado_productos_incluye_imagen_url()
    {
        $user = Usuario::create([
            'nombre' => 'Cliente',
            'apellido' => 'Test',
            'documento' => 'DOC-1',
            'email' => 'cliente@test.local',
            'password' => Hash::make('secret'),
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);
        $this->actingAs($user);

        $categoria = Categoria::create([
            'nombre' => 'General',
            'descripcion' => 'Cat',
            'estado' => 'activo',
        ]);

        Producto::create([
            'nombre' => 'Con Imagen',
            'id_categoria' => $categoria->id_categoria,
            'precio_compra' => 1,
            'precio_venta' => 2,
            'stock' => 1,
            'estado' => 'activo',
            'imagen' => 'productos/test.jpg',
        ]);

        $controller = app(\App\Http\Controllers\Api\ProductApiController::class);
        $req = \Illuminate\Http\Request::create('/api/productos','GET');
        $req->setUserResolver(fn() => $user);
        $res = $controller->index($req);
        $json = $res->getData(true);
        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);
        $this->assertArrayHasKey('imagen_url', $json['data'][0]);
        $this->assertNotNull($json['data'][0]['imagen_url']);
    }
}
