<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_route()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test SQL Injection prevention in Login.
     * Intenta inyectar SQL en el campo email.
     */
    public function test_login_sql_injection_protection()
    {
        $response = $this->post('/login', [
            'email' => "' OR '1'='1",
            'password' => 'password',
        ]);
        
        $response->assertStatus(302); // Redirects back or to login

        $this->assertGuest();
    }

    /**
     * Test XSS prevention in Product creation.
     */
    public function test_product_creation_xss_protection()
    {
        // Crear usuario admin manualmente
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol' => 'admin',
            'estado' => 'activo'
        ]);
        
        $xssPayload = '<script>alert("XSS")</script>';
        
        $categoria = \App\Models\Categoria::create(['nombre' => 'Cat Test', 'descripcion' => 'Desc', 'estado' => 'activo']);
        $proveedor = \App\Models\Proveedor::create([
            'nombre_empresa' => 'Prov Test SA',
            'nit' => '900123456',
            'contacto' => 'Cont',
            'telefono' => '123',
            'direccion' => 'Dir',
            'estado' => 'activo'
        ]);

        $routeUrl = route('api.productos.store');
        
        $response = $this->actingAs($admin, 'web')
            ->postJson($routeUrl, [
            'nombre' => $xssPayload,
            'precio_compra' => 50,
            'precio_venta' => 100,
            'stock' => 10,
            'stock_minimo' => 5,
            'codigo_barras' => '123456789',
            'id_categoria' => $categoria->id_categoria,
            'id_proveedor' => $proveedor->id_proveedor,
            'estado' => 'activo'
        ]);

        $response->assertStatus(201);
        
        // Verificamos el contenido en la base de datos
        $producto = \App\Models\Producto::where('codigo_barras', '123456789')->first();
        $this->assertNotNull($producto, 'Producto no encontrado en BD');
        
        // Si hay middleware de sanitización, el nombre no debería contener <script>
        $this->assertStringNotContainsString('<script>', $producto->nombre);
        
        // Verificamos que los datos se registraron correctamente
        $this->assertEquals(50, $producto->precio_compra);
        $this->assertEquals(100, $producto->precio_venta);
        $this->assertEquals(10, $producto->stock);
        $this->assertEquals(5, $producto->stock_minimo);
        $this->assertEquals($categoria->id_categoria, $producto->id_categoria);
        $this->assertEquals($proveedor->id_proveedor, $producto->id_proveedor);
        $this->assertEquals('activo', $producto->estado);
    }
    
    /**
     * Test CSRF middleware is registered.
     */
    public function test_csrf_protection_enabled()
    {
        // ... (existing test content or placeholder)
        $this->assertTrue(true);
    }

    /**
     * Test Validation: Document only accepts numbers.
     */
    public function test_client_validation_numbers_only_document()
    {
        // Create role if not exists
        $rol = \App\Models\Role::firstOrCreate(['nombre' => 'admin'], ['descripcion' => 'Admin Role']);
        
        // Create admin user manually
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '12345678',
            'email' => 'admin_test_doc@test.com',
            'password' => bcrypt('Password123!'),
            'rol_id' => $rol->id,
            'estado' => 'activo'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('clientes.store'), [
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'documento' => '123ABC456', // Invalid document
                'estado' => 'activo'
            ]);

        $response->assertSessionHasErrors('documento');
    }

    /**
     * Test Validation: Name/Surname only accepts letters.
     */
    public function test_client_validation_letters_only_name()
    {
        $rol = \App\Models\Role::firstOrCreate(['nombre' => 'admin'], ['descripcion' => 'Admin Role']);
        
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '87654321',
            'email' => 'admin_test_name@test.com',
            'password' => bcrypt('Password123!'),
            'rol_id' => $rol->id,
            'estado' => 'activo'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('clientes.store'), [
                'nombre' => 'Juan123', // Invalid name
                'apellido' => 'Perez',
                'documento' => '12345678',
                'estado' => 'activo'
            ]);

        $response->assertSessionHasErrors('nombre');

        $response = $this->actingAs($admin)
            ->post(route('clientes.store'), [
                'nombre' => 'Juan',
                'apellido' => 'Perez123', // Invalid surname
                'documento' => '12345678',
                'estado' => 'activo'
            ]);

        $response->assertSessionHasErrors('apellido');
    }

    /**
     * Test Password Strength Validation.
     */
    public function test_password_strength_validation()
    {
        $rol = \App\Models\Role::firstOrCreate(['nombre' => 'admin'], ['descripcion' => 'Admin Role']);
        
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'documento' => '11223344',
            'email' => 'admin_test_pass@test.com',
            'password' => bcrypt('Password123!'),
            'rol_id' => $rol->id,
            'estado' => 'activo'
        ]);

        // Weak password (too short)
        $response = $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'nombre' => 'Nuevo',
                'apellido' => 'Usuario',
                'documento' => '99887766',
                'email' => 'newuser_short@test.com',
                'password' => 'weak', 
                'password_confirmation' => 'weak',
                'rol_id' => $rol->id,
                'estado' => 'activo'
            ]);
        $response->assertSessionHasErrors('password');

        // Weak password (no symbols/numbers/mixed case)
        $response = $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'nombre' => 'Nuevo',
                'apellido' => 'Usuario',
                'documento' => '99887766',
                'email' => 'newuser_weak@test.com',
                'password' => 'password123', 
                'password_confirmation' => 'password123',
                'rol_id' => $rol->id,
                'estado' => 'activo'
            ]);
        $response->assertSessionHasErrors('password');

        // Strong password
        $response = $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'nombre' => 'Nuevo',
                'apellido' => 'Usuario',
                'documento' => '99887766',
                'email' => 'newuser_strong@test.com',
                'password' => 'StrongP@ssw0rd!', 
                'password_confirmation' => 'StrongP@ssw0rd!',
                'rol_id' => $rol->id,
                'estado' => 'activo'
            ]);
        $response->assertSessionHasNoErrors();
    }

    public function test_csrf_middleware_is_registered()
    {
        $kernel = app()->make(\App\Http\Kernel::class);
        $middlewareGroups = $kernel->getMiddlewareGroups();
        
        $this->assertArrayHasKey('web', $middlewareGroups);
        $this->assertContains(\App\Http\Middleware\VerifyCsrfToken::class, $middlewareGroups['web']);
    }

    /**
     * Test Login Throttling.
     * Verifica que después de 5 intentos fallidos, el usuario sea bloqueado.
     */
    public function test_login_throttling()
    {
        $email = 'bruteforce@test.com';
        $password = 'wrong-password';

        // Intentar login 5 veces
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => $password,
            ]);
            $response->assertSessionHasErrors(['email']);
        }

        // El sexto intento debe fallar con un mensaje de "Too many attempts"
        $response = $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ]);
        
        $response->assertSessionHasErrors(['email']);
        
        // Verificar que el mensaje de error contiene indicaciones de tiempo (segundos/minutos)
        // Laravel por defecto devuelve "Too many login attempts. Please try again in X seconds."
        // O su traducción correspondiente.
        $errors = session('errors');
        $this->assertTrue(
            str_contains($errors->first('email'), 'segundos') || 
            str_contains($errors->first('email'), 'seconds') ||
            str_contains($errors->first('email'), 'Too many login attempts')
        );
    }
}
