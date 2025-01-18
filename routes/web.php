<?php

use App\Http\Controllers\api\template\ApplicationController;
use App\Http\Controllers\api\template\ReportController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/testing', [TestController::class, "index"]);

Route::prefix('v1')->group(function () {
    Route::get('/lang/{locale}', [ApplicationController::class, 'changeLocale']);
});

Route::get('/generate-pdf', [ReportController::class, 'testReport']);

require __DIR__ . '/web/auth.php';
require __DIR__ . '/web/key.php';
