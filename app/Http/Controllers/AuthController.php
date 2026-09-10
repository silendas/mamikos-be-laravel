<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessage;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\BaseResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request);
        return BaseResponse::success(201, ResponseMessage::REGISTER_SUCCESS, $data);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request);
        return BaseResponse::success(200, ResponseMessage::LOGIN_SUCCESS, $data);
    }

    public function getMyProfile(Request $request): JsonResponse
    {
        $data = $this->authService->getMyProfile($request->user());
        return BaseResponse::success(200, ResponseMessage::USER_PROFILE_FETCHED, $data);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $data = $this->authService->updateProfile($request->user(), $request);
        return BaseResponse::success(200, ResponseMessage::USER_PROFILE_UPDATED, $data);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->user(), $request);
        return BaseResponse::success(200, ResponseMessage::PASSWORD_CHANGED, null);
    }
}

