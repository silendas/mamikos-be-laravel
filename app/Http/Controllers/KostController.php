<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessage;
use App\Http\Requests\KostRequest;
use App\Http\Responses\BaseResponse;
use App\Services\KostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KostController extends Controller
{
    protected KostService $kostService;

    public function __construct(KostService $kostService)
    {
        $this->kostService = $kostService;
    }

    public function createKost(KostRequest $request): JsonResponse
    {
        $data = $this->kostService->createKost($request->user(), $request);
        return BaseResponse::success(201, ResponseMessage::KOST_CREATED, $data);
    }

    public function updateKost(KostRequest $request, int $id): JsonResponse
    {
        $data = $this->kostService->updateKost($request->user(), $id, $request);
        return BaseResponse::success(200, ResponseMessage::KOST_UPDATED, $data);
    }

    public function deleteKost(Request $request, int $id): JsonResponse
    {
        $this->kostService->deleteKost($request->user(), $id);
        return BaseResponse::success(200, ResponseMessage::KOST_DELETED, null);
    }

    public function getKostById(int $id): JsonResponse
    {
        $data = $this->kostService->getKostById($id);
        return BaseResponse::success(200, ResponseMessage::KOST_FETCHED, $data);
    }

    public function getOwnerKosts(Request $request): JsonResponse
    {
        $data = $this->kostService->getOwnerKosts($request->user());
        return BaseResponse::success(200, ResponseMessage::KOST_FETCHED, $data);
    }

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

