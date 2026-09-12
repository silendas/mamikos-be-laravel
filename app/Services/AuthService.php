<?php

namespace App\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthService
{
    public function register(RegisterRequest $request)
    {
        if (User::where('username', $request->username)->exists()) {
            throw new BadRequestHttpException(ResponseMessage::USERNAME_ALREADY_EXISTS);
        }
        if (User::where('email', $request->email)->exists()) {
            throw new BadRequestHttpException(ResponseMessage::EMAIL_ALREADY_EXISTS);
        }

        $initialCredits = 0;
        if ($request->role === 'REGULAR_USER') {
            $initialCredits = 20;
        } elseif ($request->role === 'PREMIUM_USER') {
            $initialCredits = 40;
        } elseif ($request->role === 'OWNER') {
            $initialCredits = 0;
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'credits' => $initialCredits,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'credits' => $user->credits,
        ];
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->usernameOrEmail)
            ->orWhere('email', $request->usernameOrEmail)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new BadRequestHttpException('Invalid username/email or password');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'credits' => $user->credits,
        ];
    }

    public function getMyProfile(User $user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'credits' => $user->credits,
            'createdAt' => $user->created_at,
            'updatedAt' => $user->updated_at,
        ];
    }

    public function updateProfile(User $user, UpdateProfileRequest $request)
    {
        if ($user->username !== $request->username && User::where('username', $request->username)->exists()) {
            throw new BadRequestHttpException(ResponseMessage::USERNAME_ALREADY_EXISTS);
        }
        if ($user->email !== $request->email && User::where('email', $request->email)->exists()) {
            throw new BadRequestHttpException(ResponseMessage::EMAIL_ALREADY_EXISTS);
        }

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
        ]);

        return $this->getMyProfile($user);
    }

    public function changePassword(User $user, ChangePasswordRequest $request)
    {
        if (!Hash::check($request->currentPassword, $user->password)) {
            throw new BadRequestHttpException(ResponseMessage::INCORRECT_CURRENT_PASSWORD);
        }

        $user->update([
            'password' => Hash::make($request->newPassword),
        ]);
    }
}
