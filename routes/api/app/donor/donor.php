
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\donor\DonorController;



Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,donor:api'])->group(function () {
  Route::POST('/donor', [DonorController::class, 'store']);
  // ->middleware(["hasAddPermission:" . PermissionEnum::donor->value]);
  Route::get('/donors', [DonorController::class, 'index']);
  Route::get('/donor/{id}', [DonorController::class, 'edit']);
  Route::POST('/donor/{id}', [DonorController::class, 'update']);
  Route::post('/donor/change/password', [DonorController::class, 'changePassword']);
  // ->middleware(["userHasSubEditPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::donor_update_account_password->value]);


  // ->middleware(["userHasMainViewPermission:" . PermissionEnum::donor->value]);
});
