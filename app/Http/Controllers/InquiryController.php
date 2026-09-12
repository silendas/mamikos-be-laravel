<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessage;
use App\Http\Requests\InquiryRequest;
use App\Http\Responses\BaseResponse;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Inquiries",
    description: "Inquiry management endpoints"
)]
class InquiryController extends Controller
{
    protected InquiryService $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    #[OA\Post(
        path: "/api/inquiries",
        summary: "Ask availability for a kost",
        tags: ["Inquiries"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kostId", "message"],
                properties: [
                    new OA\Property(property: "kostId", type: "integer", example: 1),
                    new OA\Property(property: "message", type: "string", example: "Is this room still available?")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Inquiry created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function askAvailability(InquiryRequest $request): JsonResponse
    {
        $data = $this->inquiryService->askAvailability($request->user(), $request);
        return BaseResponse::success(201, ResponseMessage::INQUIRY_SUCCESS, $data);
    }

    #[OA\Get(
        path: "/api/inquiries/my-inquiries",
        summary: "Get user's inquiries",
        tags: ["Inquiries"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "User inquiries fetched successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getUserInquiries(Request $request): JsonResponse
    {
        $data = $this->inquiryService->getUserInquiries($request->user());
        return BaseResponse::success(200, ResponseMessage::INQUIRIES_FETCHED, $data);
    }
}


