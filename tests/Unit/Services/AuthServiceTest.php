<?php

namespace Tests\Unit\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_register_success(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'REGULAR_USER',
        ]);

        $result = $this->authService->register($request);

        $this->assertNotNull($result);
        $this->assertEquals('newuser', $result['username']);
        $this->assertEquals(20, $result['credits']);
    }

    public function test_register_duplicate_username_throws_exception(): void
    {
        User::create([
            'username' => 'existinguser',
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new RegisterRequest();
        $request->merge([
            'username' => 'existinguser',
            'email' => 'another@example.com',
            'password' => 'password123',
            'role' => 'REGULAR_USER',
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(ResponseMessage::USERNAME_ALREADY_EXISTS);

        $this->authService->register($request);
    }

    public function test_login_success(): void
    {
        User::create([
            'username' => 'loginuser',
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new LoginRequest();
        $request->merge([
            'usernameOrEmail' => 'loginuser',
            'password' => 'password123',
        ]);

        $result = $this->authService->login($request);

        $this->assertNotNull($result);
        $this->assertEquals('loginuser', $result['username']);
    }

    public function test_login_invalid_credentials_throws_exception(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'usernameOrEmail' => 'unknown',
            'password' => 'wrongpass',
        ]);

        $this->expectException(BadRequestHttpException::class);

        $this->authService->login($request);
    }

    public function test_get_my_profile_success(): void
    {
        $user = User::create([
            'username' => 'profileuser',
            'email' => 'profile@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $result = $this->authService->getMyProfile($user);

        $this->assertEquals('profileuser', $result['username']);
    }

    public function test_update_profile_success(): void
    {
        $user = User::create([
            'username' => 'olduser',
            'email' => 'old@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new UpdateProfileRequest();
        $request->merge([
            'username' => 'updateduser',
            'email' => 'updated@example.com',
        ]);

        $result = $this->authService->updateProfile($user, $request);

        $this->assertEquals('updateduser', $result['username']);
    }

    public function test_change_password_success(): void
    {
        $user = User::create([
            'username' => 'passuser',
            'email' => 'pass@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new ChangePasswordRequest();
        $request->merge([
            'currentPassword' => 'password123',
            'newPassword' => 'newpassword123',
        ]);

        $this->authService->changePassword($user, $request);

        $this->assertTrue(password_verify('newpassword123', $user->fresh()->password));
    }

    public function test_change_password_incorrect_current_password_throws_exception(): void
    {
        $user = User::create([
            'username' => 'passuser2',
            'email' => 'pass2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'REGULAR_USER',
            'credits' => 20,
        ]);

        $request = new ChangePasswordRequest();
        $request->merge([
            'currentPassword' => 'wrongpassword',
            'newPassword' => 'newpassword123',
        ]);

        $this->expectException(BadRequestHttpException::class);

        $this->authService->changePassword($user, $request);
    }
}
