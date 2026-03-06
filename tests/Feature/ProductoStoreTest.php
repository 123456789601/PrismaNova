<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;

class ProductoStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $categoria;
    protected $proveedor;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup roles and admin user
        $rolAdmin = Rol::create(['nombre' => 'admin']);
        $this->admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '123456789',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolAdmin->id,
            'estado' => 'activo'
        ]);

        // Setup dependencies
        $this->categoria = Categoria::create(['nombre' => 'General', 'descripcion' => 'Desc']);
        $this->proveedor = Proveedor::create([
            'nombre_empresa' => 'Prov Test',
            'contacto_nombre' => 'Juan',
            'telefono' => '123456',
            'email' => 'prov@test.com'
        ]);
    }

    /** @test */
    public function can_create_product_with_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('productos.store'), [
                'nombre' => 'Producto Test',
                'codigo_barras' => '123456',
                'id_categoria' => $this->categoria->id_categoria,
                'precio_compra' => '100.00',
                'precio_venta' => '150.00',
                'stock' => 10,
                'estado' => 'activo',
            ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('productos', ['codigo_barras' => '123456']);
    }

    /** @test */
    public function can_create_product_with_comma_decimal_price()
    {
        // Simulate input "100,50" which is common in Spanish locale
        $response = $this->actingAs($this->admin)
            ->post(route('productos.store'), [
                'nombre' => 'Producto Comma',
                'codigo_barras' => 'COMMA123',
                'id_categoria' => $this->categoria->id_categoria,
                'precio_compra' => '100,50', // Should be converted to 100.50
                'precio_venta' => '150,00', // Should be converted to 150.00
                'stock' => 5,
                'estado' => 'activo',
            ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('productos', [
            'codigo_barras' => 'COMMA123',
            'precio_compra' => 100.50,
            'precio_venta' => 150.00
        ]);
    }

    /** @test */
    public function can_create_product_with_currency_symbol()
    {
        // Simulate input "$ 100" or "S/ 100"
        $response = $this->actingAs($this->admin)
            ->post(route('productos.store'), [
                'nombre' => 'Producto Symbol',
                'codigo_barras' => 'SYMBOL123',
                'id_categoria' => $this->categoria->id_categoria,
                'precio_compra' => '$ 100.00', 
                'precio_venta' => 'S/ 150.00',
                'stock' => 5,
                'estado' => 'activo',
            ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('productos', [
            'codigo_barras' => 'SYMBOL123',
            'precio_compra' => 100.00,
            'precio_venta' => 150.00
        ]);
    }

    /** @test */
    public function validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('productos.store'), []); // Empty data

        $response->assertSessionHasErrors(['nombre', 'id_categoria', 'precio_compra', 'precio_venta', 'stock', 'estado']);
    }

    /** @test */
    public function prevents_duplicate_barcode()
    {
        Producto::create([
            'nombre' => 'Original',
            'codigo_barras' => 'DUPLICATE',
            'id_categoria' => $this->categoria->id_categoria,
            'precio_compra' => 10,
            'precio_venta' => 20,
            'stock' => 10,
            'estado' => 'activo'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('productos.store'), [
                'nombre' => 'Duplicate',
                'codigo_barras' => 'DUPLICATE',
                'id_categoria' => $this->categoria->id_categoria,
                'precio_compra' => 10,
                'precio_venta' => 20,
                'stock' => 10,
                'estado' => 'activo',
            ]);

        $response->assertSessionHasErrors('codigo_barras');
    }
}
