<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'REGULAR_USER',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'testuser')
            ->assertJsonPath('data.credits', 20);
    }

    public function test_register_invalid_input_returns_bad_request()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'INVALID_ROLE',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_register_duplicate_username()
    {
        User::create([
            'username' => 'testuser',
            'email' => 'test1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->postJson('/api/auth/register', [
            'username' => 'testuser',
            'email' => 'test2@example.com',
            'password' => 'password123',
            'role' => 'REGULAR_USER',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_login_success()
    {
        User::create([
            'username' => 'loginuser',
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'usernameOrEmail' => 'loginuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'loginuser');
    }

    public function test_login_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'usernameOrEmail' => 'nonexistent',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_get_my_profile()
    {
        $user = User::create([
            'username' => 'profileuser',
            'email' => 'profile@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'profileuser');
    }

    public function test_update_profile_success()
    {
        $user = User::create([
            'username' => 'oldusername',
            'email' => 'old@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->actingAs($user)->putJson('/api/auth/me', [
            'username' => 'newusername',
            'email' => 'new@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'newusername');
    }

    public function test_change_password_success()
    {
        $user = User::create([
            'username' => 'passuser',
            'email' => 'pass@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->actingAs($user)->putJson('/api/auth/password', [
            'currentPassword' => 'password123',
            'newPassword' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}




