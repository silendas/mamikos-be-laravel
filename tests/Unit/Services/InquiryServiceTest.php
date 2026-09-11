<?php

namespace Tests\Unit\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use App\Services\InquiryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class InquiryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InquiryService $inquiryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inquiryService = new InquiryService();
    }

    public function test_ask_availability_success(): void
    {
        $owner = User::create([
            'username' => 'ownerunit',
            'email' => 'ownerunit@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $user = User::create([
            'username' => 'userunit',
            'email' => 'userunit@example.com',
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

        $request = new InquiryRequest();
        $request->merge([
            'kostId' => $kost->id,
            'message' => 'Is this available?',
        ]);

        $response = $this->inquiryService->askAvailability($user, $request);

        $this->assertEquals('Is this available?', $response['message']);
        $this->assertEquals(15, $user->fresh()->credits);
    }

    public function test_ask_availability_insufficient_credits_throws_exception(): void
    {
        $owner = User::create([
            'username' => 'ownerpoor',
            'email' => 'ownerpoor@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $poorUser = User::create([
            'username' => 'pooruser',
            'email' => 'poor@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 2,
        ]);

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Mawar',
            'location' => 'Jakarta',
            'price' => 1500000,
            'description' => 'Nice',
            'room_count' => 5,
        ]);

        $request = new InquiryRequest();
        $request->merge([
            'kostId' => $kost->id,
            'message' => 'Is this available?',
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(ResponseMessage::INSUFFICIENT_CREDITS);

        $this->inquiryService->askAvailability($poorUser, $request);
    }

    public function test_ask_availability_kost_not_found_throws_exception(): void
    {
        $user = User::create([
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new InquiryRequest();
        $request->merge([
            'kostId' => 9999,
            'message' => 'Is this available?',
        ]);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(ResponseMessage::KOST_NOT_FOUND);

        $this->inquiryService->askAvailability($user, $request);
    }

    public function test_get_user_inquiries_success(): void
    {
        $owner = User::create([
            'username' => 'ownerget',
            'email' => 'ownerget@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 0,
        ]);

        $user = User::create([
            'username' => 'userget',
            'email' => 'userget@example.com',
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

        $responses = $this->inquiryService->getUserInquiries($user);

        $this->assertCount(1, $responses);
        $this->assertEquals('Is this available?', $responses[0]['message']);
    }
}
