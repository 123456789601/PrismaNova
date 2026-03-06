<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Rol;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $rol = Rol::create(['nombre' => 'admin']);
        $this->user = Usuario::create([
            'nombre' => 'Test',
            'apellido' => 'User',
            'documento' => '123456789',
            'email' => 'test@example.com',
            'password' => bcrypt('old-password'),
            'rol_id' => $rol->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function forgot_password_page_loads()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Recuperar Contraseña');
    }

    /** @test */
    public function reset_password_link_can_be_requested()
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function reset_password_page_loads_with_token()
    {
        $token = Password::broker()->createToken($this->user);
        
        $response = $this->get(route('password.reset', ['token' => $token]));
        
        $response->assertStatus(200);
        $response->assertSee('Nueva Contraseña');
    }

    /** @test */
    public function password_can_be_reset()
    {
        $token = Password::broker()->createToken($this->user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard'));
        
        $this->assertTrue(Hash::check('new-password', $this->user->fresh()->password));
    }
}
