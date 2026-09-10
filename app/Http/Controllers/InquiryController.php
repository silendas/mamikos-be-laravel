<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessage;
use App\Http\Requests\InquiryRequest;
use App\Http\Responses\BaseResponse;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected InquiryService $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    public function askAvailability(InquiryRequest $request): JsonResponse
    {
        $data = $this->inquiryService->askAvailability($request->user(), $request);
        return BaseResponse::success(201, ResponseMessage::INQUIRY_SUCCESS, $data);
    }

    public function getUserInquiries(Request $request): JsonResponse
    {
        $data = $this->inquiryService->getUserInquiries($request->user());
        return BaseResponse::success(200, ResponseMessage::INQUIRIES_FETCHED, $data);
    }
}

