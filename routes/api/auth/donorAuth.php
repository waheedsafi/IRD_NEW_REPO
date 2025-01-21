<?php

use App\Http\Controllers\api\auth\DonorAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key'])->group(function () {
    Route::post('/auth-donor', [DonorAuthController::class, 'login']);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'ngo:api'])->group(function () {
    Route::get('/auth-donor', [DonorAuthController::class, 'donor']);
});
