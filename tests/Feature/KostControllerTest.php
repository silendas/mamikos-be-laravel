<?php

namespace Tests\Feature;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_kost_success()
    {
        $username = 'owneruser' . rand(1000, 9999);
        $owner = User::create([
            'username' => $username,
            'email' => $username . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $response = $this->actingAs($owner)->postJson('/api/kosts', [
            'name' => 'Kost Mawar',
            'location' => 'Jakarta Selatan',
            'price' => 1500000,
            'description' => 'Comfortable kost',
            'roomCount' => 5,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Kost Mawar');
    }

    public function test_search_kosts()
    {
        $username = 'owneruser' . rand(1000, 9999);
        $owner = User::create([
            'username' => $username,
            'email' => $username . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Melati',
            'location' => 'Bandung',
            'price' => 1000000,
            'description' => 'Nice',
            'room_count' => 3,
        ]);

        $response = $this->getJson('/api/kosts/search?location=Bandung');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.totalElements', 1);
    }
}


