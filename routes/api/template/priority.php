
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\template\PriorityController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/priorities', [PriorityController::class, "priorities"])->middleware(["hasViewPermission:" . PermissionEnum::settings->value]);
  Route::delete('/priority/{id}', [PriorityController::class, "destroy"])->middleware(["hasDeletePermission:" . PermissionEnum::settings->value]);
  Route::get('/priority/{id}', [PriorityController::class, "priority"])->middleware(["hasViewPermission:" . PermissionEnum::settings->value]);
  Route::post('/priority/store', [PriorityController::class, "store"])->middleware(["hasAddPermission:" . PermissionEnum::settings->value]);
  Route::post('/priority/update', [PriorityController::class, "update"])->middleware(["hasEditPermission:" . PermissionEnum::settings->value]);
});
