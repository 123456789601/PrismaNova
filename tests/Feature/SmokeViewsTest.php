<?php
namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\View\View as ViewContract;
use Tests\TestCase;

class SmokeViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function crearAdmin()
    {
        return Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'documento' => 'ADM-SMOKE-1',
            'email' => 'admin.smoke@test.local',
            'password' => Hash::make('secret'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);
    }

    public function test_admin_puede_renderizar_vistas_principales()
    {
        $user = $this->crearAdmin();
        $this->actingAs($user);

        $controllers = [
            \App\Http\Controllers\DashboardController::class => 'index',
            \App\Http\Controllers\UsuarioController::class => 'index',
            \App\Http\Controllers\ClienteController::class => 'index',
            \App\Http\Controllers\ProveedorController::class => 'index',
            \App\Http\Controllers\CategoriaController::class => 'index',
            \App\Http\Controllers\ProductoController::class => 'index',
            \App\Http\Controllers\CompraController::class => 'index',
            \App\Http\Controllers\VentaController::class => 'index',
            \App\Http\Controllers\ReporteController::class => 'index',
            \App\Http\Controllers\CajaController::class => 'index',
        ];
        foreach ($controllers as $ctrl => $method) {
            $instance = $this->app->make($ctrl);
            $resp = $instance->{$method}();
            $this->assertTrue($resp instanceof ViewContract, 'No devuelve vista en '.$ctrl.'@'.$method);
        }
    }
}
