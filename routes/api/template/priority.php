
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\PriorityController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/priorities', [PriorityController::class, "priorities"])->middleware(["userHasSubViewPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_priority->value]);
  Route::delete('/priority/{id}', [PriorityController::class, "destroy"])->middleware(["userHasSubDeletePermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_priority->value]);
  Route::get('/priority/{id}', [PriorityController::class, "priority"])->middleware(["userHasSubViewPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_priority->value]);
  Route::post('/priority/store', [PriorityController::class, "store"])->middleware(["userHasSubAddPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_priority->value]);
  Route::post('/priority/update', [PriorityController::class, "update"])->middleware(["userHasSubEditPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_priority->value]);
});
