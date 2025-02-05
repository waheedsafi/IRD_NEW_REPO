
<?php

use App\Http\Controllers\api\app\agreement\AgreementController;
use App\Http\Controllers\api\app\director\DirectorController;
use Illuminate\Support\Facades\Route;





Route::get('/ngo/agreement/{ngo_id}', [AgreementController::class, 'agreement']);




Route::prefix('v1')->group(function () {});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {});

// ngo user 


Route::prefix('v1')->middleware(['api.key', "authorized:" . 'ngo:api'])->group(function () {});
