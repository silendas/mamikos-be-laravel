<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'getMyProfile']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('kosts')->group(function () {
    Route::get('/search', [KostController::class, 'searchKosts']);
    Route::get('/{id}', [KostController::class, 'getKostById']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [KostController::class, 'createKost']);
        Route::put('/{id}', [KostController::class, 'updateKost']);
        Route::delete('/{id}', [KostController::class, 'deleteKost']);
        Route::get('/owner/my-kosts', [KostController::class, 'getOwnerKosts']);
    });
});

Route::prefix('inquiries')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [InquiryController::class, 'askAvailability']);
    Route::get('/my-inquiries', [InquiryController::class, 'getUserInquiries']);
});

