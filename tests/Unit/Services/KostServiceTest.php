<?php

namespace Tests\Unit\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\KostRequest;
use App\Models\Kost;
use App\Models\User;
use App\Services\KostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class KostServiceTest extends TestCase
{
    use RefreshDatabase;

    private KostService $kostService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kostService = new KostService();
    }

    public function test_create_kost_success(): void
    {
        $owner = User::create([
            'username' => 'owner1',
            'email' => 'owner1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $request = new KostRequest();
        $request->merge([
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice kost',
            'roomCount' => 5,
        ]);

        $result = $this->kostService->createKost($owner, $request);

        $this->assertEquals('Kost Mawar', $result['name']);
    }

    public function test_create_kost_non_owner_throws_exception(): void
    {
        $regularUser = User::create([
            'username' => 'regular1',
            'email' => 'regular1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new KostRequest();
        $request->merge([
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice kost',
            'roomCount' => 5,
        ]);

        $this->expectException(AccessDeniedHttpException::class);

        $this->kostService->createKost($regularUser, $request);
    }

    public function test_update_kost_success(): void
    {
        $owner = User::create([
            'username' => 'owner2',
            'email' => 'owner2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Old Name',
            'location' => 'Jakarta',
            'price' => 1000000,
            'description' => 'Old desc',
            'room_count' => 3,
        ]);

        $request = new KostRequest();
        $request->merge([
            'name' => 'Updated Name',
            'location' => 'Jakarta Selatan',
            'price' => 2000000,
            'description' => 'Updated desc',
            'roomCount' => 10,
        ]);

        $result = $this->kostService->updateKost($owner, $kost->id, $request);

        $this->assertEquals('Updated Name', $result['name']);
    }

    public function test_delete_kost_success(): void
    {
        $owner = User::create([
            'username' => 'ownerdel',
            'email' => 'ownerdel@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Del',
            'location' => 'Jakarta',
            'price' => 1000000,
            'description' => 'Desc',
            'room_count' => 3,
        ]);

        $this->kostService->deleteKost($owner, $kost->id);

        $this->assertDatabaseMissing('kosts', ['id' => $kost->id]);
    }

    public function test_get_kost_by_id_success(): void
    {
        $owner = User::create([
            'username' => 'ownergetbyid',
            'email' => 'ownergetbyid@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Get',
            'location' => 'Jakarta',
            'price' => 1000000,
            'description' => 'Desc',
            'room_count' => 3,
        ]);

        $result = $this->kostService->getKostById($kost->id);

        $this->assertEquals('Kost Get', $result['name']);
    }

    public function test_get_kost_by_id_not_found_throws_exception(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->kostService->getKostById(9999);
    }

    public function test_search_kosts_success(): void
    {
        $owner = User::create([
            'username' => 'ownersearch',
            'email' => 'ownersearch@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice',
            'room_count' => 5,
        ]);

        $result = $this->kostService->searchKosts('Mawar', 'Jakarta', 1000000, 2000000, 'asc', 0, 10);

        $this->assertEquals(1, $result['totalElements']);
        $this->assertEquals('Kost Mawar', $result['content'][0]['name']);
    }
}
