
<?php

use App\Http\Controllers\api\template\log\UserLoginLogController;
use App\Http\Controllers\api\template\LogController;
use Illuminate\Support\Facades\Route;



Route::get('/user/activities', [UserLoginLogController::class, "logs"]);

Route::prefix('v1')->middleware("authorized:" . 'user:api')->group(function () {
    Route::get('/file-logs', [LogController::class, "fileLogs"]);
    Route::get('/database-logs', [LogController::class, "databaseLogs"]);
    Route::post('/logs/clear', [LogController::class, "clear"]);
});
