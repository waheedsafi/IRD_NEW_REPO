<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use App\Http\Controllers\api\template\DestinationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/destinations', [DestinationController::class, "destinations"])->middleware(["userHasViewPermission:" . PermissionEnum::settings->value]);
  Route::get('/destination/{id}', [DestinationController::class, "destination"])->middleware(["userHasViewPermission:" . PermissionEnum::settings->value]);
  Route::get('/complete-destinations', [DestinationController::class, "completeDestinations"]);
  Route::get('/muqams', [DestinationController::class, "muqams"]);
  Route::get('/directorates', [DestinationController::class, "directorates"]);
  Route::post('/destination/store', [DestinationController::class, "store"])->middleware(["userHasAddPermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_destination->value]);
  Route::delete('/destination/{id}', [DestinationController::class, "destroy"])->middleware(["userHasDeletePermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_destination->value]);
  Route::post('/destination/update', [DestinationController::class, "update"])->middleware(["userHasEditPermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_destination->value]);
});
