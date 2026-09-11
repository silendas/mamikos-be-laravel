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
        $ownerUsername = 'owneruser' . rand(1000, 9999);
        $owner = User::create([
            'username' => $ownerUsername,
            'email' => $ownerUsername . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $userUsername = 'regularuser' . rand(1000, 9999);
        $user = User::create([
            'username' => $userUsername,
            'email' => $userUsername . '@example.com',
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


