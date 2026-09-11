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
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Auth",
    description: "Authentication endpoints"
)]
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[OA\Post(
        path: "/api/auth/register",
        summary: "Register new user",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation", "role"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "role", type: "string", example: "seeker")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Register success"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request);
        return BaseResponse::success(201, ResponseMessage::REGISTER_SUCCESS, $data);
    }

    #[OA\Post(
        path: "/api/auth/login",
        summary: "Login user",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Login success"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request);
        return BaseResponse::success(200, ResponseMessage::LOGIN_SUCCESS, $data);
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Get current user profile",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Profile fetched successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getMyProfile(Request $request): JsonResponse
    {
        $data = $this->authService->getMyProfile($request->user());
        return BaseResponse::success(200, ResponseMessage::USER_PROFILE_FETCHED, $data);
    }

    #[OA\Put(
        path: "/api/auth/me",
        summary: "Update current user profile",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe Updated"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john_updated@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profile updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $data = $this->authService->updateProfile($request->user(), $request);
        return BaseResponse::success(200, ResponseMessage::USER_PROFILE_UPDATED, $data);
    }

    #[OA\Put(
        path: "/api/auth/password",
        summary: "Change current user password",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["current_password", "new_password", "new_password_confirmation"],
                properties: [
                    new OA\Property(property: "current_password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "new_password", type: "string", format: "password", example: "newpassword123"),
                    new OA\Property(property: "new_password_confirmation", type: "string", format: "password", example: "newpassword123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Password changed successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->user(), $request);
        return BaseResponse::success(200, ResponseMessage::PASSWORD_CHANGED, null);
    }
}


