<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\CreditScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_recharge_user_credits_success(): void
    {
        $regular = User::create([
            'username' => 'regular',
            'email' => 'regular@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 5,
        ]);

        $premium = User::create([
            'username' => 'premium',
            'email' => 'premium@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PREMIUM_USER',
            'credits' => 10,
        ]);

        $owner = User::create([
            'username' => 'owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
            'role' => 'OWNER',
            'credits' => 50,
        ]);

        $service = new CreditScheduleService();
        $service->rechargeUserCredits();

        $this->assertEquals(20, $regular->fresh()->credits);
        $this->assertEquals(40, $premium->fresh()->credits);
        $this->assertEquals(0, $owner->fresh()->credits);
    }
}
