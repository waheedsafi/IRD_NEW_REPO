
<?php

use App\Http\Controllers\api\app\agreement\AgreementController;
use App\Http\Controllers\api\app\ngo\ExtendNgoController;
use Illuminate\Support\Facades\Route;







Route::get('/ngo/agreement/documents/{agreement_id}', [AgreementController::class, 'agreementDocument']);

Route::prefix('v1')->group(function () {});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/ngo/agreement/{ngo_id}', [AgreementController::class, 'agreement']);

  // this route use for store new information for extend the ngo
  Route::post('/ngo/agreement/extend', [ExtendNgoController::class, 'extendNgoAgreement']);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'ngo:api'])->group(function () {});
