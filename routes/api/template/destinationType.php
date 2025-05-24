<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\template\DestinationTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  // Route::post('/destination-type/store', [DestinationTypeController::class, "store"])->middleware(["hasAddPermission:" . PermissionEnum::settings->value]);
  // Route::delete('/destination-type/{id}', [DestinationTypeController::class, "destroy"])->middleware(["hasDeletePermission:" . PermissionEnum::settings->value]);
  // Route::post('/destination-type/update', [DestinationTypeController::class, "update"])->middleware(["hasEditPermission:" . PermissionEnum::settings->value]);
});

Route::prefix('v1')->group(function () {
  Route::get('/destination-types', [DestinationTypeController::class, "destinationTypes"]);
  Route::get('/destination-type/{id}', [DestinationTypeController::class, "destinationType"]);
});
