<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Rol;
use App\Models\MetodoPago;
use App\Models\Configuracion;
use Illuminate\Support\Facades\View;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $cliente;
    protected $venta;
    protected $producto;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuración básica del sistema
        Configuracion::create(['clave' => 'nombre_tienda', 'valor' => 'Tienda de Prueba', 'tipo' => 'texto']);
        Configuracion::create(['clave' => 'direccion_tienda', 'valor' => 'Calle Falsa 123', 'tipo' => 'texto']);
        Configuracion::create(['clave' => 'moneda', 'valor' => '$', 'tipo' => 'texto']);
        Configuracion::create(['clave' => 'mensaje_ticket', 'valor' => 'Gracias por su visita', 'tipo' => 'texto']);

        // Refrescar configuración compartida para que la vista la vea
        View::share('configuracion', Configuracion::pluck('valor', 'clave')->all());

        // Crear rol admin y usuario
        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        
        $this->usuario = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
            'estado' => 'activo',
            'documento' => '123456789'
        ]);

        // Crear cliente
        $this->cliente = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Test',
            'documento' => '987654321',
            'telefono' => '555-5555',
            'email' => 'cliente@test.com',
            'direccion' => 'Calle Test 123',
            'estado' => 'activo'
        ]);

        // Crear categoría y proveedor
        $categoria = \App\Models\Categoria::create(['nombre' => 'General', 'estado' => 'activo']);
        $proveedor = \App\Models\Proveedor::create(['nombre_empresa' => 'Prov Test', 'contacto' => 'Juan', 'estado' => 'activo']);

        // Crear producto
        $this->producto = Producto::create([
            'codigo_barras' => '123456',
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripcion Test',
            'precio_compra' => 50.00,
            'precio_venta' => 100.00,
            'stock' => 100,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor
        ]);

        // Crear venta con detalles
        $this->venta = Venta::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_usuario' => $this->usuario->id_usuario,
            'fecha' => now(),
            'subtotal' => 200.00,
            'descuento' => 20.00,
            'impuesto' => 34.20, // 19% de (200-20)
            'total' => 214.20,
            'metodo_pago' => 'Efectivo',
            'estado' => 'completada'
        ]);

        DetalleVenta::create([
            'id_venta' => $this->venta->id_venta,
            'id_producto' => $this->producto->id_producto,
            'cantidad' => 2,
            'precio_unitario' => 100.00,
            'subtotal' => 200.00
        ]);
    }

    /** @test */
    public function puede_ver_el_ticket_de_venta()
    {
        $response = $this->actingAs($this->usuario)->get(route('ventas.ticket', $this->venta));

        $response->assertStatus(200);
        
        // Verificar información de la tienda
        $response->assertSee('Tienda de Prueba');
        $response->assertSee('Calle Falsa 123');
        
        // Verificar información de la venta
        $response->assertSee('#' . str_pad($this->venta->id_venta, 8, '0', STR_PAD_LEFT));
        $response->assertSee($this->cliente->nombre);
        $response->assertSee($this->usuario->nombre);
        
        // Verificar detalles financieros
        $response->assertSee(number_format(200.00, 2)); // Subtotal
        $response->assertSee(number_format(20.00, 2)); // Descuento
        $response->assertSee(number_format(34.20, 2)); // Impuesto
        $response->assertSee(number_format(214.20, 2)); // Total
        
        // Verificar método de pago
        $response->assertSee('Efectivo');
        
        // Verificar mensaje de pie de página
        $response->assertSee('Gracias por su visita');
    }

    /** @test */
    public function no_muestra_descuento_si_es_cero()
    {
        // Crear venta sin descuento
        $venta = Venta::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_usuario' => $this->usuario->id_usuario,
            'fecha' => now(),
            'subtotal' => 100.00,
            'descuento' => 0.00,
            'impuesto' => 0.00,
            'total' => 100.00,
            'metodo_pago' => 'Tarjeta',
            'estado' => 'completada'
        ]);

        $response = $this->actingAs($this->usuario)->get(route('ventas.ticket', $venta));

        $response->assertStatus(200);
        $response->assertDontSee('Descuento:');
    }
}
