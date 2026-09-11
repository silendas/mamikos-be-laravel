<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessage;
use App\Http\Requests\KostRequest;
use App\Http\Responses\BaseResponse;
use App\Services\KostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Kosts",
    description: "Kost management endpoints"
)]
class KostController extends Controller
{
    protected KostService $kostService;

    public function __construct(KostService $kostService)
    {
        $this->kostService = $kostService;
    }

    #[OA\Post(
        path: "/api/kosts",
        summary: "Create a new kost",
        tags: ["Kosts"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "description", "address", "price", "room_available"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Kost Melati"),
                    new OA\Property(property: "description", type: "string", example: "Comfortable kost near campus"),
                    new OA\Property(property: "address", type: "string", example: "Jl. Merdeka No. 10"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 1500000),
                    new OA\Property(property: "room_available", type: "integer", example: 5)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Kost created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function createKost(KostRequest $request): JsonResponse
    {
        $data = $this->kostService->createKost($request->user(), $request);
        return BaseResponse::success(201, ResponseMessage::KOST_CREATED, $data);
    }

    #[OA\Put(
        path: "/api/kosts/{id}",
        summary: "Update an existing kost",
        tags: ["Kosts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Kost ID", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Kost Melati Updated"),
                    new OA\Property(property: "description", type: "string", example: "Updated description"),
                    new OA\Property(property: "address", type: "string", example: "Jl. Merdeka No. 12"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 1600000),
                    new OA\Property(property: "room_available", type: "integer", example: 3)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Kost updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Kost not found")
        ]
    )]
    public function updateKost(KostRequest $request, int $id): JsonResponse
    {
        $data = $this->kostService->updateKost($request->user(), $id, $request);
        return BaseResponse::success(200, ResponseMessage::KOST_UPDATED, $data);
    }

    #[OA\Delete(
        path: "/api/kosts/{id}",
        summary: "Delete a kost",
        tags: ["Kosts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Kost ID", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Kost deleted successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Kost not found")
        ]
    )]
    public function deleteKost(Request $request, int $id): JsonResponse
    {
        $this->kostService->deleteKost($request->user(), $id);
        return BaseResponse::success(200, ResponseMessage::KOST_DELETED, null);
    }

    #[OA\Get(
        path: "/api/kosts/{id}",
        summary: "Get kost by ID",
        tags: ["Kosts"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Kost ID", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Kost fetched successfully"),
            new OA\Response(response: 404, description: "Kost not found")
        ]
    )]
    public function getKostById(int $id): JsonResponse
    {
        $data = $this->kostService->getKostById($id);
        return BaseResponse::success(200, ResponseMessage::KOST_FETCHED, $data);
    }

    #[OA\Get(
        path: "/api/kosts/owner/my-kosts",
        summary: "Get owner's kosts",
        tags: ["Kosts"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Owner kosts fetched successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getOwnerKosts(Request $request): JsonResponse
    {
        $data = $this->kostService->getOwnerKosts($request->user());
        return BaseResponse::success(200, ResponseMessage::KOST_FETCHED, $data);
    }

    #[OA\Get(
        path: "/api/kosts/search",
        summary: "Search kosts",
        tags: ["Kosts"],
        parameters: [
            new OA\Parameter(name: "name", in: "query", required: false, description: "Filter by name", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "location", in: "query", required: false, description: "Filter by location", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "minPrice", in: "query", required: false, description: "Minimum price", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "maxPrice", in: "query", required: false, description: "Maximum price", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "sort", in: "query", required: false, description: "Sort order", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "page", in: "query", required: false, description: "Page number", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "size", in: "query", required: false, description: "Page size", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Kosts search results fetched successfully")
        ]
    )]
    public function searchKosts(Request $request): JsonResponse
    {
        $name = $request->query('name');
        $location = $request->query('location');
        $minPrice = $request->query('minPrice') !== null ? (float) $request->query('minPrice') : null;
        $maxPrice = $request->query('maxPrice') !== null ? (float) $request->query('maxPrice') : null;
        $sort = $request->query('sort');
        $page = (int) $request->query('page', 0);
        $size = (int) $request->query('size', 10);

        $data = $this->kostService->searchKosts($name, $location, $minPrice, $maxPrice, $sort, $page, $size);
        return BaseResponse::success(200, ResponseMessage::KOST_FETCHED, $data);
    }
}

