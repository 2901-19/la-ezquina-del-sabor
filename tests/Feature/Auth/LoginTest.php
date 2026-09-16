<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(): Usuario
    {
        $role = Role::create(['nombre' => 'Test', 'descripcion' => 'Test role']);

        return Usuario::create([
            'rol_id' => $role->id,
            'username' => 'testuser',
            'password_hash' => bcrypt('password123'),
            'nombre_completo' => 'Test User',
            'activo' => true,
        ]);
    }

    public function test_el_formulario_de_login_se_muestra(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_con_credenciales_validas(): void
    {
        $user = $this->createUserWithRole();

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_con_credenciales_invalidas(): void
    {
        $this->createUserWithRole();

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $user = $this->createUserWithRole();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
