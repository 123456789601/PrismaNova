<?php
namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SmokeViewsRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function crearUsuario($rol, $doc)
    {
        return Usuario::create([
            'nombre' => ucfirst($rol),
            'apellido' => 'Test',
            'documento' => $doc,
            'email' => $rol.'.smoke@test.local',
            'password' => Hash::make('secret'),
            'rol' => $rol,
            'estado' => 'activo',
        ]);
    }

    public function test_cajero_puede_ver_vistas_de_caja_y_ventas()
    {
        $user = $this->crearUsuario('cajero', 'CAJ-SMOKE-1');
        $this->actingAs($user);
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\VentaController::class)->index());
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\CajaController::class)->index());
    }

    public function test_bodeguero_puede_ver_vistas_de_inventario()
    {
        $user = $this->crearUsuario('bodeguero', 'BOD-SMOKE-1');
        $this->actingAs($user);
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\ProveedorController::class)->index());
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\CategoriaController::class)->index());
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\ProductoController::class)->index());
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\CompraController::class)->index());
    }

    public function test_cliente_puede_ver_mis_compras_y_tienda()
    {
        $user = $this->crearUsuario('cliente', 'CLI-SMOKE-1');
        $this->actingAs($user);
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\MisComprasController::class)->index());
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $this->app->make(\App\Http\Controllers\TiendaController::class)->catalogo());
    }
}
