
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\ngo\ExtendNgoController;
use App\Http\Controllers\api\app\agreement\AgreementController;



Route::prefix('v1')->middleware(['api.key', "doubleAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::get('/ngo/agreement/{ngo_id}', [AgreementController::class, 'agreement'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_agreement->value]);
  Route::get('/ngo/agreement-documents', [AgreementController::class, 'agreementDocuments'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_agreement->value]);
  Route::get('/ngo/agreement/documents/{agreement_id}', [AgreementController::class, 'agreementDocument'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_agreement->value]);
  Route::get('/ngo/missing/register/signed/form/{ngo_id}', [AgreementController::class, 'missingRegisterSignedForm'])->middleware(["userHasSubViewPermission:" . PermissionEnum::ngo->value . "," . SubPermissionEnum::ngo_agreement->value]);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  // this route use for store new information for extend the ngo
  Route::post('/ngo/agreement/extend', [ExtendNgoController::class, 'extendNgoAgreement']);
});
