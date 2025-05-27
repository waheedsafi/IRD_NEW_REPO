
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\StatusController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  // NGO
  Route::get('/statuses/ngo/{id}', [StatusController::class, 'ngoStatuses'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_status->value]);
  Route::get('/statuses/ngo/modify', [StatusController::class, 'ngoStatusTypes'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_status->value]);
  Route::post('/statuses/ngo/modify', [StatusController::class, 'changeNgoStatus'])->middleware(["userHasSubAddPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_status->value]);

  // Agreement
  Route::get('/statuses/agreements/{id}', [StatusController::class, 'agreementStatuses'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_agreement_status->value]);
});
