<?php

namespace Tests\Feature;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ask_availability_success()
    {
        $owner = User::create([
            'username' => 'owneruser',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $user = User::create([
            'username' => 'regularuser',
            'email' => 'regular@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Melati',
            'location' => 'Bandung',
            'price' => 1000000,
            'description' => 'Nice',
            'room_count' => 3,
        ]);

        $response = $this->actingAs($user)->postJson('/api/inquiries', [
            'kostId' => $kost->id,
            'message' => 'Is this room available?',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.message', 'Is this room available?');

        $this->assertEquals(15, $user->fresh()->credits);
    }
}

