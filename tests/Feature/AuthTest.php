<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * A basic feature test valid credentials.
     */
    public function a_user_can_login_with_valid_credentials(): void
    {
         // Arrange: create user in test DB
        User::factory()->create([
            'email' => 'admin@portal.me',
            'password' => Hash::make('Password@1'),
        ]);

        // Act: send login request
        $response = $this->postJson('api/login', [
            'email' => 'admin@portal.me', // remove space
            'password' => 'Password@1'
        ]);

        // Assert: check response
        $response->assertStatus(200)
                ->assertJsonStructure(['status']);
    }

    /**
     * @test
     * To check authorization
     */
    public function authenticated_user_can_access_protected_route()
    {
        Role::create(['name' => "admin", 'guard_name' => "web"]);

        $user = User::factory()->create([
            'email' => 'admin@portal.me',
            'password' => Hash::make('Password@1'),
        ]);
        $user->assignRole('admin');
        
        $this->actingAs($user, 'web')
            ->getJson('/api/v1/admin')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }
}
