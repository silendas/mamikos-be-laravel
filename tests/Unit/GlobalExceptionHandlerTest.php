<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_resource_not_found_returns_not_found(): void
    {
        $response = $this->getJson('/api/kosts/9999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_handle_bad_request_exception_returns_bad_request(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'usernameOrEmail' => 'nonexistent',
            'password' => 'wrong',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_handle_unauthorized_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }
}
