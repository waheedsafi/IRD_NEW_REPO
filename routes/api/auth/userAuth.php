<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\auth\NgoAuthController;

Route::prefix('v1')->middleware(['api.key'])->group(function () {
    Route::post('/auth-user', [AuthController::class, 'login']);

    Route::get('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
    Route::post('/auth-logout', [AuthController::class, 'logout']);
    Route::get('/auth-user', [AuthController::class, 'user']);
    Route::post('/auth/user/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/user/change-permissions', [AuthController::class, 'changePermissions']);
    Route::delete('/auth/user/delete/{id}', [AuthController::class, "delete"]);
});
