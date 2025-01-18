
<?php

use App\Http\Controllers\api\template\LocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
    Route::get('/contries', [LocationController::class, "contries"]);
    Route::get('/provinces', [LocationController::class, "provinces"]);
    Route::get('/districts', [LocationController::class, 'districts']);
});
