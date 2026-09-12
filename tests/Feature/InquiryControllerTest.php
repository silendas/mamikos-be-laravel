<?php

namespace Tests\Feature;

use App\Models\Inquiry;
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

    public function test_ask_availability_insufficient_credits()
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
            'credits' => 2,
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

        $response->assertStatus(400)
            ->assertJsonPath('success', false);

        $this->assertEquals(2, $user->fresh()->credits);
    }

    public function test_get_user_inquiries_success()
    {
        $owner = User::create([
            'username' => 'ownerinquiry',
            'email' => 'ownerinquiry@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $user = User::create([
            'username' => 'userinquiry',
            'email' => 'userinquiry@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice',
            'room_count' => 5,
        ]);

        Inquiry::create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'message' => 'Is this available?',
        ]);

        $response = $this->actingAs($user)->getJson('/api/inquiries/my-inquiries');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.message', 'Is this available?');
    }

    public function test_ask_availability_invalid_input_returns_bad_request()
    {
        $user = User::create([
            'username' => 'invalidinquiryuser',
            'email' => 'invalidinquiry@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->actingAs($user)->postJson('/api/inquiries', [
            'kostId' => null,
            'message' => '',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }
}



