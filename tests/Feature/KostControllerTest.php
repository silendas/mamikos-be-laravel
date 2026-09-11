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

    public function test_create_kost_invalid_input_returns_bad_request()
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
            'name' => '',
            'location' => '',
            'price' => -100,
            'description' => '',
            'roomCount' => 0,
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_create_kost_forbidden_for_regular_user()
    {
        $username = 'regularuser' . rand(1000, 9999);
        $user = User::create([
            'username' => $username,
            'email' => $username . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $response = $this->actingAs($user)->postJson('/api/kosts', [
            'name' => 'Kost Mawar',
            'location' => 'Jakarta Selatan',
            'price' => 1500000,
            'description' => 'Comfortable kost',
            'roomCount' => 5,
        ]);

        $response->assertStatus(403);
    }

    public function test_get_kost_by_id_success()
    {
        $owner = User::create([
            'username' => 'ownerid',
            'email' => 'ownerid@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice kost',
            'room_count' => 5,
        ]);

        $response = $this->getJson("/api/kosts/{$kost->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $kost->id);
    }

    public function test_update_kost_success()
    {
        $owner = User::create([
            'username' => 'ownerupdate',
            'email' => 'ownerupdate@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Old Kost Name',
            'location' => 'Jakarta',
            'price' => 1000000,
            'description' => 'Old desc',
            'room_count' => 3,
        ]);

        $response = $this->actingAs($owner)->putJson("/api/kosts/{$kost->id}", [
            'name' => 'Updated Kost Name',
            'location' => 'Jakarta Selatan',
            'price' => 2000000,
            'description' => 'Updated desc',
            'roomCount' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated Kost Name');
    }

    public function test_delete_kost_success()
    {
        $owner = User::create([
            'username' => 'ownerdelete',
            'email' => 'ownerdelete@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost to Delete',
            'location' => 'Jakarta',
            'price' => 1000000,
            'description' => 'Desc',
            'room_count' => 3,
        ]);

        $response = $this->actingAs($owner)->deleteJson("/api/kosts/{$kost->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
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



