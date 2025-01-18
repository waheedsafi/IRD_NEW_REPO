
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\donor\DonorController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(['api.key', "authorized:" . 'donor:api'])->group(function () {
  Route::POST('donor/store', [DonorController::class, 'store'])->middleware(["hasAddPermission:" . PermissionEnum::donor->value]);
  Route::get('/donors', [DonorController::class, 'donors'])->middleware(["hasViewPermission:" . PermissionEnum::donor->value]);
});
